<?php

namespace App\Query;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class StringAgg extends FunctionNode
{
    public $isDistinct = false;
    public $pathExp = null;
    public $separator = null;
    public $orderBy = null;

    /**
     * @param Parser $parser
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function parse(Parser $parser):void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $lexer = $parser->getLexer();
//        if ($lexer->isNextToken(Lexer::T_DISTINCT)) {
//            $parser->match(Lexer::T_DISTINCT);
//
//            $this->isDistinct = true;
//        }

        // first Path Expression is mandatory
        $this->pathExp = array();
        $this->pathExp[] = $parser->SingleValuedPathExpression();

        while ($lexer->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->pathExp[] = $parser->StringPrimary();
        }

        if ($lexer->isNextToken(Lexer::T_ORDER)) {
            $this->orderBy = $parser->OrderByClause();
        }

        if ($lexer->isNextToken(Lexer::T_IDENTIFIER)) {
            if (strtolower($lexer->lookahead['value']) !== 'separator') {
                $parser->syntaxError('separator');
            }
            $parser->match(Lexer::T_IDENTIFIER);

            $this->separator = $parser->StringPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $result = 'STRING_AGG(' . ($this->isDistinct ? 'DISTINCT ' : '');

        $fields = array();
        foreach ($this->pathExp as $pathExp) {
            $fields[] = $pathExp->dispatch($sqlWalker);
        }

        $result .= sprintf('%s', implode(', ', $fields));

        if ($this->orderBy) {
            $result .= ' ' . $sqlWalker->walkOrderByClause($this->orderBy);
        }

        if ($this->separator) {
            $result .= ' SEPARATOR ' . $sqlWalker->walkStringPrimary($this->separator);
        }

        $result .= ')';

        return $result;
    }

}
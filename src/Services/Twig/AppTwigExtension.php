<?php

// src/Services/Twig/AppTwigExtension.php
namespace App\Services\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppTwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('nomMajMin', [$this, 'MajMin']),
        ];
    }

    public function MajMin($nom,$option)
    {
        if ($option == 'maj'){
            $nom = strtoupper($nom);
        }
        if ($option == 'min'){
            $nom = strtolower($nom);
        }

        return $nom;
    }
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


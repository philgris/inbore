
{% extends 'index.html.twig' %}

{# INDEX : <?= $entity_class_name ?> #}

{# initialize the maximum number of fields list to show #}
{% set max_fields_visible = 5 %}
{# set the search field column to show with an asterix #}
{% set search_field_column = '<?= lcfirst($entity_class_name) ?>_bootgrid_search_column' %}

{# initialize the array of linked entities ([entity1, entity2 ...]) to which we wish to make a link-shortcuts
1) The table of results should include a collumn of the type: <th data-column-id = "linkEntity" data-formatter = "linkEntity" data-sortable = "false"> Col. </ Th>
2) the controller will have to return in the table rows the colloids of the type: "linkEntity1" => "patern1", "linkeEntity2" => "patern2" ... #}

{% set links =  [] %}
{% for row in list_SQL_fetchedVariables %}
    {% if row[:5] == "link_" %} 
            {% set links = links|merge([row[5:]]) %}
    {% endif %}                                                    
{% endfor %}

{% block action %}
	{{ parent() }}
{% endblock %}

{% block body %}
    
        {#  {{dump(list_SQL_fetchedVariables)}} #}
    
    
	{# definition of columns to display 
					RQ! for sortable columns, suffix the field names with the names of tables tableName.fieldName #}

	<div class="table-responsive">
		<div class="table-responsive">
			<div class="table-responsive">
				<table id="grid-basic" class="table table-condensed table-hover table-striped">
					<thead>
                                            <tr>
                                            {% set compt = 0 %}                                                       
                                            {% for row in list_SQL_fetchedVariables %}
                                                    {% if row == 'id' %}
                                                        {% if search_field_column == 'id' %}
                                                            <th data-column-id="id" data-width="10" data-type="numeric" data-visible="true">Id&nbsp;*</th>
                                                        {% else %}
                                                            <th data-column-id="id" data-width="10" data-type="numeric" data-visible="false">Id</th>
                                                        {% endif %} 
                                                    {% elseif row == 'date_maj' %}
                                                        <th data-column-id="date_maj" data-width="100" data-type="date">{{'list.date_maj'|trans}}</th>
                                                    {% elseif row == 'date_cre' %}
                                                        <th data-column-id="date_cre" data-width="100" data-visible="false" data-type="date">{{'list.date_cre'|trans}}</th>
                                                    {% elseif row == 'user_maj' %}
                                                        <th data-column-id="user_maj" data-width="10" data-visible="false">{{'list.user_maj'|trans}}</th>
                                                    {% elseif row == 'user_cre' %}
                                                        <th data-column-id="user_cre" data-width="10" data-visible="false">{{'list.user_cre'|trans}}</th>
                                                    {% elseif row == 'user_cre_id' %}
                                                    {% elseif row[:5] == "link_" %}
                                                        <th data-column-id="{{ row }}" data-formatter="{{ row }}" data-sortable="false" data-width="20" data-visible="true">{{('list.'~row)|trans}}</th>                                                   
                                                    {% else %}
                                                        {% if compt < max_fields_visible  %}
                                                            {% if search_field_column == row %}
                                                                <th data-column-id="{{ row }}" data-visible="true">{{('list.'~row)|trans}}&nbsp;*</th>
                                                            {% else %}
                                                                <th data-column-id="{{ row }}" data-visible="true">{{('list.'~row)|trans}}</th>
                                                            {% endif %}
                                                        {% else %}
                                                            <th data-column-id="{{ row }}" data-visible="false">{{('list.'~row)|trans}}</th>
                                                        {% endif %} 
                                                        {% set compt = compt + 1 %}
                                                    {% endif %}                                                                                                            
                                            {% endfor %}
                                            
                                            <th data-column-id="show" data-formatter="show" data-sortable="false" data-width="20">{{'list.show'|trans}}</th>
                                            <th data-column-id="edit" data-formatter="edit" data-sortable="false" data-width="20">{{'list.edit'|trans}}</th>
                                            <th data-column-id="delete" data-formatter="delete" data-sortable="false" data-width="20">{{'list.delete'|trans}}</th>
                                            
                                            </tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
{% endblock %}


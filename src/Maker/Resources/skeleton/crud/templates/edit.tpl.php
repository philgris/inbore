{% extends 'edit.html.twig' %}

{# EDIT : <?= strtoupper ( $entity_class_name ) ?> #}


{% block stylesheets %}
	{{ parent() }}
	{# Load for specific CSS with this TWIG call : {{ encore_entry_link_tags("<?= strtolower($entity_class_name) ?>-form") }} 
         (i) copy the CSS file <?= strtolower($entity_class_name) ?>-form.css in  /asset/Core/forms/css/ directory
         (ii)add the Style entry in  the configuration file webpack.config.js :
         .addStyleEntry('contact-form', './assets/Core/forms/css/<?= strtolower($entity_class_name) ?>-form.css') 
        #}
{% endblock %}


{% block action %}
	{{parent()}}
        {# Add a button Back to linked entity : name_of_linked_entity
            {% if edit_form.vars.data.nameOfLinkedEntityFk is not null %}
                    <a href="{{path('name_of_linked_entity_show', {id: edit_form.vars.data.nameOfLinkedEntityFk.id})}}" class="btn btn-sm btn-info">
                            <i class="fas fa-link"></i>
                            {{'button.Back to name_of_linked_entity'|trans}}
                    </a>
            {% endif %}
        #}
{% endblock %}

{% block body %}
	{{ parent() }}
{% endblock %}


{% block scripts %}
	{{ parent() }}
        {# Load for specific js script with the TWIG call : {{ encore_entry_script_tags("<?= strtolower($entity_class_name) ?>-form") }} 
         (i) copy the file <?= strtolower($entity_class_name) ?>-form.js in /asset/Core/forms/js/ directory
         (ii)add the js entry in the configuration file webpack.config.js  : 
         .addEntry('<?= strtolower($entity_class_name) ?>-form', './assets/Core/forms/js/<?= strtolower($entity_class_name) ?>-form.js') 
        #}
{% endblock %}


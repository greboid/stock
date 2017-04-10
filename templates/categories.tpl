{% macro catMenu(data) %}
  {% import _self as macros %}
  <ul>
  {% for entry in data %}
    <li>
      <a href="/category/{{ entry['name'] }}">{{ entry['name'] }}</a>
      {% if entry['subcategories'] is defined and entry['subcategories']|count > 0 %}
        <ul>
          {{ macros.catMenu(entry['subcategories']) }}
        </ul>
      {% endif %}
    </li>
  {% endfor %}
  </ul>
{% endmacro %}

{% import _self as macros %}
{{ include('header.tpl') }}
{{ include('menu.tpl') }}
  <div class="container-fluid">
      <div class="row">
          <div class="col">
          </div>
          <div class="col align-self-center">
              <h1 class="text-center">Categories</h1>
              {{ macros.catMenu(categories) }}
          </div>
          <div class="col">
          </div>
      </div>
  </div>
{{ include('footer.tpl') }}

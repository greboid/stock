{% macro catMenu(data, level) %}
    {% import _self as macros %}
    {% set nbsp = '&nbsp;&nbsp;&nbsp;' %}
    {% for entry in data %}
        <option value="{{ repeat(nbsp,level)|raw }}{{ entry.name }}">{{ repeat(nbsp,level)|raw }}{{ entry.name }}</option>
        {% if entry['subcategories'] is defined and entry['subcategories']|count > 0 %}
            {{ macros.catMenu(entry['subcategories'], level+1) }}
        {% endif %}
    {% endfor %}
{% endmacro %}
{% import _self as macros %}
{{ include('header.tpl') }}
{{ include('menu.tpl') }}
    <div class="container-fluid">
        <div class="fs row">
            <div class="col bg-faded sidebar">
            <h1>Filters</h1>
              <form id="itemsearchform" class="input-group">
                        <input type="text" id="itemsearch" class="form-control" placeholder="Filter items">
                        <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
                <form id="sitesearchform" class="input-group">
                    <select id="sitesearch" class="form-control" multiple style="width: 100%">
                        {% for site in sites %}
                            <option>{{ site }}</option>
                        {% endfor %}
                    </select>
                </form>
                <form id="locationsearchform" class="input-group">
                    <select id="locationsearch" class="form-control" multiple style="width: 100%">
                        {% for siteID, site in locations %}
                            <optgroup label="{{ site['name'] }}">
                            {% for locationID, location in site['locations'] %}
                                <option value="{{ location }}">{{ location }}</option>
                            {% endfor %}
                            </optgroup>
                        {% endfor %}
                    </select>
                </form>
                <form id="categorysearchform" class="input-group">
                    <select id="categorysearch" class="form-control" multiple style="width: 100%">
                        {{ macros.catMenu(categories, 0) }}
                    </select>
                </form>
                <form id="mincountform" class="input-group">
                    <input type="number" id="mincount" class="form-control" placeholder="Minimum count" min="0">
                    <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
                <form id="maxcountform" class="input-group">
                    <input type="number" id="maxcount" class="form-control" placeholder="Maximum count" min="0">
                    <button type="reset" class="input-group-addon fa fa-times" aria-hidden="true"></button>
                </form>
            </div>
            <main class="col-8">
                <h1>Stock: {{ site }}</h1>
                <table id="stock" class="table table-hover table-bordered dataTable">
                    <thead class="thead-default">
                        <tr>
                            <th class="text-center">Item</th>
                            <th class="text-center">Site</th>
                            <th class="text-center">Location</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for id, item in stock %}
                            <tr data-itemid="{{ id }}" data-itemmax="{{ item.max }}" data-itemmin="{{ item.min }}">
                                <td class="name align-middle">{{ item.name }}</td>
                                <td class="align-middle">{{ item.site }}</td>
                                <td class="align-middle">{{ item.location }}</td>
                                <td class="align-middle">{{ item.category }}</td>
                                <td data-order="{{ item.count }}" data-search="{{ item.count }}" class="align-middle">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button
                                                class="itemcountbutton btn btn-sm btn-secondary"
                                                value="-2"
                                                {% if item.count < 2 %} disabled{% endif %}>
                                                &laquo;
                                            </button>
                                        </span>
                                        <span class="input-group-btn">
                                            <button
                                                class="itemcountbutton btn btn-sm btn-secondary"
                                                value="-1"
                                                {% if item.count == 0 %} disabled{% endif %}>
                                                &lsaquo;
                                            </button>
                                        </span>
                                        <input
                                            class="{% if item.count <= item.min %}belowmin {% endif %}itemcount form-control"
                                            type="number"
                                            name="{{ id }}-count"
                                            value="{{ item.count }}"
                                            required
                                            min="0"
                                            max="{{ max_stock}}">
                                        <span class="input-group-btn">
                                            <button
                                                class="itemcountbutton btn btn-sm btn-secondary"
                                                value="1"
                                                {% if item.count == max_stock %} disabled{% endif %}>
                                                &rsaquo;
                                            </button>
                                        </span>
                                        <span class="input-group-btn">
                                            <button
                                                class="itemcountbutton btn btn-sm btn-secondary"
                                                value="2"
                                                {% if item.count > (max_stock-2) %} disabled{% endif %}>
                                                &raquo;
                                            </button>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </main>
            <div class="col-2">
            </div>
        </div>
    </div>
{{ include('footer.tpl') }}

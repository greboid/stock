{% macro catMenu(data, level) %}
    {% import _self as macros %}
    {% set nbsp = '&nbsp;&nbsp;&nbsp;' %}
    {% for entry in data %}
        <option value="{{ entry.id }}">{{ repeat(nbsp,level)|raw }}{{ entry.name }}</option>
        {% if entry['subcategories'] is defined and entry['subcategories']|count > 0 %}
            {{ macros.catMenu(entry['subcategories'], level+1) }}
        {% endif %}
    {% endfor %}
{% endmacro %}
{% import _self as macros %}
{{ include('header.tpl') }}
{{ include('menu.tpl') }}
    <div class="container-fluid">
        <div class="row">
            <div class="col">
            </div>
            <div class="col-8 align-self-center">
                <h1 class="text-center">Manage Items</h1>
                <table id="items" class="table table-hover table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th class="text-center">Item Name</th>
                            <th class="text-center">Location Name</th>
                            <th class="text-center">Site</th>
                            <th class="text-center">Category</th>
                            <th class="text-center"># Stock</th>
                            <th class="table-actions text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for id, item in stock %}
                            <tr>
                                <form method="post">
                                        <td class="align-middle">{{ item['name'] }}</td>
                                        <td class="align-middle">{{ item['location'] }}</td>
                                        <td class="align-middle">{{ item['site'] }}</td>
                                        <td class="align-middle">{{ item['category'] }}</td>
                                        <td class="align-middle">{{ item['count'] }}</td>
                                        <td class="align-middle">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button type="button"
                                                            class="btn btn-primary"
                                                            data-toggle="modal"
                                                            data-target="#editItemModal"
                                                            data-itemid="{{ item['id'] }}"
                                                            data-itemname="{{ item['name'] }}"
                                                            data-locationname="{{ item['location'] }}"
                                                            data-categoryname="{{ item['category'] }}"
                                                            data-stockcount="{{ item['count'] }}">
                                                        Edit
                                                    </button>
                                                </span>
                                                <span class="input-group-btn">
                                                    <button formaction="/item/delete/{{ id }}"
                                                            class="btn btn-danger">
                                                        Delete
                                                    </button>
                                                </span>
                                            </div>
                                        </td>
                                </form>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#addItemModal">
                    Add Item
                </button>
            </div>
            <div class="col">
            </div>
        </div>
    </div>

    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col align-self-center">
                        <form method="post" action="/item/add" id="addItemForm">
                            <input type="hidden" id="action" name="action" value="additem">
                            <fieldset>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="name">Name</label>
                                    <input class="col form-control" id="name" name="name" type="text" placeholder="name" required>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="location">Location</label>
                                    <select class="col form-control" id="location" name="location" required>
                                        <option selected=""></option>
                                        {% for siteID, site in locations %}
                                            <optgroup label="{{ site['name'] }}">
                                            {% for locationID, location in site['locations'] %}
                                                <option value="{{ locationID }}">{{ location }}</option>
                                            {% endfor %}
                                            </optgroup>
                                        {% endfor %}
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="category">Category</label>
                                    <select class="col form-control" id="category" name="category" required>
                                        <option selected=""></option>
                                        {{ macros.catMenu(categories) }}
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="count">Initial Stock Count</label>
                                    <input class="col form-control" id="count" name="count" type="number" placeholder="Initial count" required min="0" max="{$max_stock}">
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="addItemForm" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col align-self-center">
                        <form method="post" action="/item/edit" id="editItemForm">
                            <input type="hidden" id="editID" name="editID" value="">
                            <fieldset>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="editName">Name</label>
                                    <input class="col form-control" id="editName" name="editName" type="text" placeholder="name" required>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="editLocation">Location</label>
                                    <select class="col form-control" id="editLocation" name="editLocation" required>
                                        <option selected=""></option>
                                        {% for siteID, site in locations %}
                                            <optgroup label="{{ site['name'] }}">
                                            {% for locationID, location in site['locations'] %}
                                                <option value="{{ locationID }}">{{ location }}</option>
                                            {% endfor %}
                                            </optgroup>
                                        {% endfor %}
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="category">Category</label>
                                    <select class="col form-control" id="editCategory" name="editCategory" required>
                                        <option selected=""></option>
                                        {{ macros.catMenu(categories) }}
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label" for="editCount">Initial Stock Count</label>
                                    <input class="col form-control" id="editCount" name="editCount" type="number" placeholder="Initial count" required min="0" max="{{ max_stock }}">
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="editItemForm" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
{{ include('footer.tpl') }}

{{ include('header.tpl') }}
{{ include('menu.tpl') }}
  <div class="container-fluid">
      <div class="row">
          <div class="col">
          </div>
          <div class="col align-self-center">
              <h1 class="text-center">Locations</h1>
              <ul>
              <li>
                  <a class="pure-menu-link" href="/site/all">All</a>
              </li>
              {% for siteid, site in sites %}
                <li class="pure-menu-item">
                    <a class="pure-menu-link" href="/site/{{ site }}">{{ site }}</a>
                    <ul>
                        {% for location in locations[siteid]['locations'] %}
                            {% for loc in location %}
                            <li>
                                <a class="pure-menu-link" href="/location/{{ loc }}">{{ loc }}</a>
                            </li>
                            {% endfor %}
                        {% endfor %}
                    </ul>
                </li>
              {% endfor %}
              </ul>
          </div>
          <div class="col">
          </div>
      </div>
  </div>
{{ include('footer.tpl') }}

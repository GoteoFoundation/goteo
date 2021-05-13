<?php $this->layout('impact_discover/layout') ?>

<?php $this->section('impact-discover-content') ?>


<div class="section impact-discover-filters">
                        <div class="container">
                            <div class="row">
                              <div class="col-xs-12 col-sm-9" id="filters-mobile">
                                <a href="" class="filter"><img src="./assets/img/icons/funnel.svg" /> FILTRAR</a>
                                <a class="close flip" href="#"><i class="icon icon-close"></i></a>
                              </div>
                              <div class="col-xs-12 col-sm-4" id="filters-footprints">
                                <p>Filtra por Huellas de Goteo</p>
                                <ul>
                                  <li><a href="" data-footprint="all" class="active">Todas</a></li>
                                  <li><a href="" data-footprint="eco">Eco</a></li>
                                  <li><a href="" data-footprint="soc">Soc</a></li>
                                  <li><a href="" data-footprint="dem">Dem</a></li>
                                </ul>
                              </div>
                              <div class="col-xs-12 col-sm-3" id="filters-ods-list">
                                <p>Filtra por Objetivos de Desarrollo Sostenible</p>
                                <ul>
                                  <li>
                                    <input type="checkbox" name="ods1" />
                                    <img src="./assets/img/ods/ods1.svg" />
                                    1. No poverty
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods2" />
                                    <img src="./assets/img/ods/ods2.svg" />
                                    2. Zero hunger
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods3" />
                                    <img src="./assets/img/ods/ods3.svg" />
                                    3. Good Health and Well-Being
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods4" />
                                    <img src="./assets/img/ods/ods4.svg" />
                                    4. Quality Education
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods5" />
                                    <img src="./assets/img/ods/ods5.svg" />
                                    5. Igualdad de género
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods6" />
                                    <img src="./assets/img/ods/ods6.svg" />
                                    6. Agua limpia y saneamiento
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods7" />
                                    <img src="./assets/img/ods/ods7.svg" />
                                    7. Energía asequible y no contaminante
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods8" />
                                    <img src="./assets/img/ods/ods8.svg" />
                                    8. Trabajo decente y crecimiento económico
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods9" />
                                    <img src="./assets/img/ods/ods9.svg" />
                                    9. Industria, innovación e infraestructura
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods10" />
                                    <img src="./assets/img/ods/ods10.svg" />
                                    10. Reducción de desigualdades
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods11" />
                                    <img src="./assets/img/ods/ods11.svg" />
                                    11. Ciudades y comunidades sostenibles
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods12" />
                                    <img src="./assets/img/ods/ods12.svg" />
                                    12. Producción y consumo responsables
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods13" />
                                    <img src="./assets/img/ods/ods13.svg" />
                                    13. Acción por el clima
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods14" />
                                    <img src="./assets/img/ods/ods14.svg" />
                                    14. Vida submarina
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods15" />
                                    <img src="./assets/img/ods/ods15.svg" />
                                    15. Vida de ecosistemas terrestres
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods16" />
                                    <img src="./assets/img/ods/ods16.svg" />
                                    16. Paz, justicia e instituciones sólidas
                                  </li>
                                  <li>
                                    <input type="checkbox" name="ods17" />
                                    <img src="./assets/img/ods/ods17.svg" />
                                    17. Alianzas para lograr los objetivos
                                  </li>
                                </ul>
                                <p>
                                  <a href="" id="reset-ods">Borrar todo</a>
                                  <button>Aplicar filtros</button>
                                </p>
                              </div>
                              <div class="col-xs-12 col-sm-5 text-center" id="filters-ods-select">
                                <select>
                                  <option>Filtra por Objetivos de Desarrollo sostenible</option>
                                  <option data-footprints="eco">ODS1</option>
                                  <option data-footprints="soc">ODS2</option>
                                  <option data-footprints="dem">ODS3</option>
                                  <option data-footprints="eco,soc">ODS4</option>
                                  <option data-footprints="soc,dem">ODS5</option>
                                  <option data-footprints="eco,dem">ODS6</option>
                                  <option data-footprints="eco,doc,dem">ODS7</option>
                                </select>
                              </div>
                              <div class="col-xs-12 col-sm-3 text-right" id="filters-view-as">
                                <a href=""><img src="./assets/img/icons/mosaic.svg"></a>
                                <a href="" class="active"><img src="./assets/img/icons/lists.svg"></a>
                                <a href=""><img src="./assets/img/icons/map.svg"></a>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="section impact-discover-projects">
                        <div class="container">
                            <h1>Busca un proyecto por Huellas o ODS</h1>
                            <div class="row" id="ods-icons">
                              <div class="col col-xs-12 col-sm12">

                              </div>
                            </div>
                            <div class="row">
                              <div class="col col-xs-12 col-sm-8">
                                <img src="https://via.placeholder.com/420x250?text=img+project" class="bg-project eco">
                                <div class="project-footprint">
                                  <img src="assets/img/footprint/footprint-ico-dem.svg" alt="Huella DEM" class="footprint" />
                                </div>
                                <div class="project-description">
                                  <h2>Como tu</h2>
                                  <h3>Implícate y apoya a las investigadoras a llevar la Ciencia y Tecnología a los centros educativos</h3>
                                  <p>Por CyT@UMA</p>
                                </div>
                              </div>
                              <div class="col col-xs-12 col-sm-4">
                                <div class="row">
                                  <div class="col col-xs-12 col-sm-12">
                                    <img src="https://via.placeholder.com/420x250?text=img+project" class="bg-project soc">
                                    <div class="project-footprint">
                                      <img src="assets/img/footprint/footprint-ico-eco.svg" alt="Huella ECO" class="footprint" />
                                    </div>
                                    <div class="project-description">
                                      <h2>Construïm L'Espora: llibreria i espai cooperatiu a L'H!</h2>
                                      <p>Por CyT@UMA</p>
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col col-xs-12 col-sm-12">
                                    <img src="https://via.placeholder.com/420x250?text=img+project" class="bg-project dem">
                                    <div class="project-footprint">
                                      <img src="assets/img/footprint/footprint-ico-eco.svg" alt="Huella ECO" class="footprint" />
                                    </div>
                                    <div class="project-description">
                                      <h2>Como tu</h2>
                                      <h3>Implícate y apoya a las investigadoras a llevar la Ciencia y Tecnología a los centros educativos</h3>
                                      <p>Por CyT@UMA</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col col-xs-12 col-sm-4">
                                <img src="https://via.placeholder.com/420x250?text=img+project" class="bg-project dem">
                                <div class="project-footprint">
                                  <img src="assets/img/footprint/footprint-ico-eco.svg" alt="Huella ECO" class="footprint" />
                                </div>
                                <div class="project-description">
                                  <h2>Construïm L'Espora: llibreria i espai cooperatiu a L'H!</h2>
                                  <p>Por CyT@UMA</p>
                                </div>
                              </div>
                              <div class="col col-xs-12 col-sm-4">
                                <img src="https://via.placeholder.com/420x250?text=img+project" class="bg-project soc">
                                <div class="project-footprint">
                                  <img src="assets/img/footprint/footprint-ico-eco.svg" alt="Huella ECO" class="footprint" />
                                </div>
                                <div class="project-description">
                                  <h2>Dale cuerda a 'Climática'</h2>
                                  <p>Por CyT@UMA</p>
                                </div>
                              </div>
                              <div class="col col-xs-12 col-sm-4">
                                <img src="https://via.placeholder.com/420x250?text=img+project" class="bg-project eco">
                                <div class="project-footprint">
                                  <img src="assets/img/footprint/footprint-ico-eco.svg" alt="Huella ECO" class="footprint" />
                                </div>
                                <div class="project-description">
                                  <h2>Como tu</h2>
                                  <h3>Implícate y apoya a las investigadoras a llevar la Ciencia y Tecnología a los centros educativos</h3>
                                  <p>Por CyT@UMA</p>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col col-xs-12 col-sm-4">
                                <div class="row">
                                  <div class="col col-xs-12 col-sm-12">
                                    <img src="https://via.placeholder.com/420x250?text=img+project" class="bg-project soc">
                                    <div class="project-footprint">
                                      <img src="assets/img/footprint/footprint-ico-eco.svg" alt="Huella ECO" class="footprint" />
                                    </div>
                                    <div class="project-description">
                                      <h2>Tragone - DESconectar para Reconectar.</h2>
                                      <p>Por CyT@UMA</p>
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col col-xs-12 col-sm-12">
                                    <img src="https://via.placeholder.com/420x250?text=img+project" class="bg-project dem">
                                    <div class="project-footprint">
                                      <img src="assets/img/footprint/footprint-ico-eco.svg" alt="Huella ECO" class="footprint" />
                                    </div>
                                    <div class="project-description">
                                      <h2>Tragone - DESconectar para Reconectar.</h2>
                                      <p>Por CyT@UMA</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col col-xs-12 col-sm-8">
                                <img src="https://via.placeholder.com/420x250?text=img+project" class="bg-project eco">
                                <div class="project-footprint">
                                  <img src="assets/img/footprint/footprint-ico-eco.svg" alt="Huella ECO" class="footprint" />
                                </div>
                                <div class="project-description">
                                  <h2>Documental Parias de la Tierra</h2>
                                  <h3>Implícate y apoya a las investigadoras a llevar la Ciencia y Tecnología a los centros educativos</h3>
                                  <p>Por CyT@UMA</p>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
      const odsList = {
        "ods" : [
          {
            "id": "ods1",
            "ods": "ODS1",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods2",
            "ods": "ODS2",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods3",
            "ods": "ODS3",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods4",
            "ods": "ODS5",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods5",
            "ods": "ODS5",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods6",
            "ods": "ODS6",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods7",
            "ods": "ODS7",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods8",
            "ods": "ODS8",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods9",
            "ods": "ODS9",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods10",
            "ods": "ODS10",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods11",
            "ods": "ODS11",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods12",
            "ods": "ODS12",
            "footprints": ["all","soc"],
          },
          {
            "id": "ods13",
            "ods": "ODS13",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods14",
            "ods": "ODS14",
            "footprints": ["all","eco","soc"],
          },
          {
            "id": "ods15",
            "ods": "ODS15",
            "footprints": ["all","eco","soc","dem"],
          },
          {
            "id": "ods16",
            "ods": "ODS16",
            "footprints": ["all","eco"],
          },
          {
            "id": "ods17",
            "ods": "ODS17",
            "footprints": ["all","eco"],
          },
        ]
      }
    </script>


<?php $this->replace() ?>

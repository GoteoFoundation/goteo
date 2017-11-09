<?php
$this->layout('layout', [
    'bodyClass' => 'channel',
    'title' =>  $this->text('regular-channel').' '.$this->channel->name,
    'meta_description' => $this->channel->description
    ]);

$this->section('header-navbar-brand'); 

?>
    <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= $this->asset('img/goteo.svg') ?>" class="logo" alt="Goteo"></a>

<?php

$this->replace();

$this->section('content');

?>


    <?= $this->insert("channel/partials/owner_info") ?>
    
    <?= $this->insert("channel/partials/call_action") ?>

    <?= $this->insert("channel/partials/summary_matchfunding_section"); ?>
    
    <div class="projects-section">
        <div class="container-fluid">
            <div id="content">
                <?= $this->supply('channel-content') ?>
            </div>

        </div>
    </div>

    <?= $this->insert("channel/partials/summary_section"); ?>


<!-- Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="poolModalLabel">Bases para participar en el canal</h4>
      </div>
      <div class="modal-body">
            <h3>1. ¿Qué es Ahora coMparte?</h3>

            <p>
                Ahora coMparte es el canal de Ahora Madrid en la red social Goteo, para compartir recursos y cofinanciar ideas y proyectos que mejoren los barrios de Madrid.
            </p>
            <p>
            El reglamento económico de AM destina el 20% de las donaciones de sus cargos electos y personal contratado a proyectos sociales, para financiar iniciativas que mejoren el bien común de la sociedad madrileña y el desarrollo ciudadano.
            </p>
            <p>
            La Fundación GOTEO es una entidad sin ánimo de lucro que ha impulsado goteo.org, una red social social para la financiación colectiva (aportaciones monetarias) y colaboración distribuida (servicios, infraestructuras, microtareas y otros recursos), desde la que se impulsa, gracias al compromiso de la ciudadanía, el desarrollo autónomo de proyectos creativos e innovadores, cuyos fines sean de carácter social, cultural, científico, educativo, tecnológico o ecológico. Goteo es, pues, una plataforma de captación de micromecenazgo para que personas y organizaciones puedan llevar a cabo con éxito proyectos socialmente rentables, sostenibles y perdurables en el tiempo.
            </p>
            <p>
            Ahora Madrid y la Fundación GOTEO han acordado la creación de un CANAL para la distribución de estas donaciones, denominado Ahora coMparte dentro de la plataforma goteo.org, plataforma de crowdfunding.
            Ahora coMparte es un proyecto para financiar proyectos que contribuyan a la mejora de la vida, el medio ambiente y la convivencia en la ciudad de Madrid y/o tengan como meta el empoderamiento ciudadano, el pro-común y contribuyan a extender los valores de ciudadanía que propone Ahora Madrid en su programa.
            </p>
            <p>
            Con este proyecto se pretende impulsar iniciativas sociales acordes con la apuesta municipalista de Ahora Madrid.
            </p>

            <h3>2. ¿A quién va dirigido? Requisitos</h3>
            <p>
            Pueden presentar proyectos en el canal creado específicamente, http://ahoramadrid.goteo.org, personas físicas y jurídicas, asociaciones, colectivos, fundaciones, cooperativas y PYMES con un fin social y sin ánimo de lucro. Las empresas tendrán que cumplir los requisitos de igualdad en el ámbito laboral recogidos en la L.O. 3/2007, de 22 de marzo, de igualdad efectiva entre mujeres y hombres.
            </p>
            <p>
            El ámbito de actuación del proyecto es la ciudad de Madrid y sus principios deben ir en línea con valores de igualdad, construcción de comunidad, fines sociales, pro-común y/o de acuerdo a la filosofía del programa político de Ahora Madrid.
            </p>
            <p>
            Los proyectos se revisarán desde la perspectiva de realizarse en Madrid (geolocalización, descripción del proyecto, links) y con los criterios habituales de Goteo (ver https://www.goteo.org/faq). Los proyectos deben ser abiertos y estar sujetos a licencias Creative Commons o GPL.
            </p>
            Se financiarán iniciativas que mejoren el medio ambiente, trabajen en la diversidad y la convivencia, fomenten la participación, contribuyan a la cultura libre, movilidad sostenible, la educación, la rehabilitación de espacios sociales, acción social, software y hardware libres, economía alternativa, arquitectura, gastronomía, investigación, emprendizaje; que contribuyan al enriquecimiento de los bienes comunes a partir de retornos colectivos, gracias a la utilización de licencias libres y/o abiertas.
            Formatos: Puede ser una idea de producto, un servicio, una investigación, una iniciativa, una red, una nueva tecnología, un elemento de difusión y concienciación... Lo importante es que contribuyan a mejorar la calidad de vida en nuestra ciudad y el empoderamiento ciudadano, de una manera sostenible.

            <h3>3. Selección de los proyectos</h3>

            <p>
            Los proyectos que quieran participar en el canal presentarán sus propuestas creando un usuario y rellenando un formulario de recepción de proyectos habilitado en la página web de esta canal, http://ahoramadrid.goteo.org, previo registro de un perfil de usuario en http://goteo.org
            Los proyectos que se incorporarán al canal de crowdfunding serán validados por Ahora Madrid en base a propuestas previas de Goteo, o que se hagan desde el propio Ahora Madrid.
            </p>
            <p>
            a) El/la promotor/a del proyecto tendrá que aceptar en su panel de usuario la aceptación o rechazo para formar
            parte del canal Ahora coMparte y poder optar a la bolsa. Este botón estará disponible hasta que se actúe
            sobre él.
            <br>
            
            b) Si el/la promotor/a del proyecto acepta, empezará o continuará (si ya estaba en campaña en Goteo antes) su
            campaña dentro del canal.
            <br>
            
            c) Los y las participantes deberán cumplimentar todos los campos requeridos en dicho formulario (título,
            descripción breve, presupuesto, características básicas, objetivos de la campaña de micromecenazgo,
            recompensas individuales y retornos colectivos que se ofrecen, colaboraciones solicitadas, experiencia previa
            y descripción del equipo, así como el presupuesto y calendario de trabajo, etc.)
            <br>
            
            d) Los/as asesores/s de GOTEO informarán a los/as impulsores/as del procedimiento establecido y en caso en
            que estén interesados en formar parte del Canal Ahora coMparte, retendrán los proyectos hasta el primer
            lunes de cada mes, fecha en la cual publicarán los proyectos.
            <br>
            
            e) Ahora Madrid accederá a la información del nuevo proyecto y podrá ver el proyecto, descartándolo si
            considera que no puede entrar en el canal y optar al matchfunding. Para tomar esta decisión dispondrá como
            máximo hasta el dia 20 de cada mes, fecha a partir de la cual deberá haber tomada una decisión sobre la
            pertenencia o no al canal Ahora coMparte.
            <br>
            
            f) Si Ahora Madrid descarta un proyecto, éste no pertenecerá al canal ni optará al matchfunding de Ahora
            Madrid. El proyecto podrá, sin embargo, publicarse en goteo.org y publicar allí su campaña de crowdfunding,
            como otro de los proyectos de la plataforma.
            <br>
            
            g) La Fundación Goteo no se hace responsable de la valoración hecha por Ahora Madrid y de los proyectos
            aceptados o rechazados por el canal Ahora coMparte. La Fundación GOTEO y los asesores de la plataforma
            goteo.org no responderán a cuestiones relacionadas con la entrada o no en el canal Ahora coMparte se
            debe escribir al correo ahoracomparte@ahoramadrid.org
            <br>
            </p>

            <h3>4. Distribución del dinero, ¿cómo funciona el matchfunding en Ahora coMparte?</h3>
            <p>
            El matchfunding de Ahora Madrid se adjudicará a los proyectos al concluir el tiempo de campaña, y siempre queésta hubiera resultado exitosa, siguiendo las siguientes condiciones de distribución:
            </p>
            a) Como todos los proyectos en goteo.org, para que un proyecto sea exitoso, debe conseguir su
            presupuesto mínimo en los primeros 40 días de campaña.
            <br>
            b) Para optar al matchfunding el proyecto debe ser exitoso, por lo tanto también debe alcanzar su
            financiación mínima dentro de los primeros 40 días de campaña.
            <br>
            c) Si transcurridos los primeros 40 días, los proyectos no consiguen llegar a su objetivo mínimo de
            financiación, tanto las aportaciones recaudadas como el matchfunding asociado a esas aportaciones se
            retornará a los micromecenas y el matchfunding quedará disponible para otros proyectos.
            <br>
            d) Si el proyecto alcanza su financiación mínima en los 40 primeros días, se tomarán en consideración el
            número de aportaciones ciudadanas, proporcional a su presupuesto mínimo. Se seguirá el siguiente
            cálculo:
            <br>
            <ul>
                <li>Presupuesto mínimo del proyecto de hasta 2.999 euros: 20 micromecenas.</li>
                <li>Presupuesto mínimo del proyecto de entre 3.000 hasta 5.999 euros: 40 micromecenas.</li>
                <li>Presupuesto mínimo del proyecto de entre 6.000 hasta 8.999 euros: 60 micromecenas.</li>
            </ul>
            Y así en adelante, sumando 20 micromecenas por cada 3000€ de presupuesto mínimo.
            <br>
            e) Cuando el proyecto haya alcanzado su presupuesto mínimo así como el número de micromecenas
            estipulado en función de su presupuesto, el proyecto entrará en la 2a ronda de la campaña de financiación
            y se le hará desde Ahora Madrid una aportación única que cubra la diferencia entre el presupuesto mínimo
            y el óptimo, con un límite máximo de 5.000€.
            <br>
            f) Si acaba la ronda de 40 días y no ha conseguido el presupuesto mínimo, el dinero se devuelve a los
            micromecenas. Si ha conseguido el presupuesto mínimo pero no ha llegado al número de micromecenas
            estipulado en función del presupuesto, el proyecto no tendrá posibilidad de acceder al matchfunding. Aún
            así, podrá seguir su campaña de financiación en la plataforma goteo.org.
            <br>

            <h3>5. Pago de las aportaciones del crowdfunding y del matchfunding</h3>
            <p>
            El pago del matchfunding y las donaciones ciudadanas se realizarán de forma conjunta al término de las campañas de crowdfunding.
            </p>
            <p>
            Una vez finalizados los 80 días de campaña y determinadas y subsanadas las posibles incidencias en cobros a
            cofinanciadores, se realizará el pago al agente o equipo promotor de cada proyecto junto a un informe económico
            que específica la suma total de todas las aportaciones realizadas por TPV, las realizadas por Paypal con las
            comisiones y el matchfunding vinculado.
            </p>
            <p>
            La Fundación Goteo descontará del importe total de la aportación efectuada por terceros en las dos rondas de financiación, los costes del servicio y las comisiones bancarias correspondientes, según el siguiente desglose:
            </p>
            <p>
            El 4% (más impuestos) en concepto de costes y servicio de FG, de aplicación a los importes de las
            donaciones efectuadas por terceros particulares.
            </p>
            <p>
            El 0,8% en los importes de las donaciones efectuadas por terceros particulares con tarjetas de crédito.
            </p>
            <p>
            El 3,4% + 0,36 (por transacción) en las donaciones efectuadas por terceros particulares a través de la plataforma de pago PayPal.
            </p>
            <p>
            En los casos en los que las campañas de crowdfunding de los proyectos a financiar no alcancen el presupuesto mínimo de financiación definido en el proyecto, el servicio no estará sujeto a ningún coste por parte de la plataforma. La parte de matchfunding no estará sujeta al pago de comisiones.
            </p>
            <p>
            Las aportaciones individuales en todo momento estarán reflejadas en la página pública de cada una de las campañas.
            </p>
            <p>
            El pago se efectuará, en el plazo máximo de 80 días y previa la firma de un contrato para la producción del proyecto en la Fundación Goteo y el agente o equipo promotor de cada proyecto.
            </p>
      </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>
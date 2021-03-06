<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

/**
 * Controlador para proteger los controladores que heredan
 * Para empezar a crear una convención de seguridad y módulos
 *
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */

//Cargo los modelos
Load::models('sistema/usuario', 'sistema/menu');

class SgcController extends Controller {

    /**
    * Titulo de la página
    */
    public $page_title = 'Página sin título';

    /**
    * Nombre del módulo en el que se encuentra
    */
    public $page_module = 'Indefinido';

    /**
    * Tipo de formato del reporte
    */
    public $page_format;

    /**
     * Variable que indica el cambio de título de la página en las respuestas ajax
     */
    public $set_title = true;

    /**
     * Callback que se ejecuta antes de los métodos de todos los controladores
     */
    final protected function initialize() {
        /**
         * Si el método de entrada es ajax, el tipo de respuesta es sólo la vista
         */
        if(Input::isAjax()) {
            View::template('sgc/sgc');
        }

        /**
         * Verifico que haya iniciado sesión
         */
        if( !DwAuth::isLogged() ) {
            //Verifico que no genere una redirección infinita
            if( ($this->controller_name != 'login') && ( $this->action_name != 'entrar' && $this->action_name != 'salir') ) {
                DwMessage::warning('No has iniciado sesión o ha caducado.');
                //Verifico que no sea una ventana emergente
                if($this->module_name == 'reporte') {
                    View::error();//TODO: crear el método error()
                } else {
                    DwRedirect::toLogin('sistema/login/entrar/');
                }
                return false;
            }
        } else if( DwAuth::isLogged() && $this->controller_name!='login' ) {
            $acl = new DwAcl(); //Cargo los permisos y templates
            if(APP_UPDATE && (Session::get('perfil_id') != Perfil::SUPER_USUARIO) ) { //Solo el super usuario puede hacer todo
                if($this->module_name!='dashboard' && $this->controller_name!='index') {
                    $msj = 'Estamos en labores de actualización y mantenimiento.';
                    $msj.= '<br />';
                    $msj.= 'El servicio se reanudará dentro de '.APP_UPDATE_TIME;
                    if(Input::isAjax()) {
                        View::update();
                    } else {
                        DwMessage::info($msj);
                        DwRedirect::to("dashboard");
                    }
                    return FALSE;
                }
            }
            if (!$acl->check(Session::get('perfil_id'))) {
                DwMessage::error('Tu no posees privilegios para acceder a <b>' . Router::get('route') . '</b>');
                (Input::isAjax()) ? View::ajax() : View::select(NULL);
                return false;
            }
            if(!defined('SKIN')) {
                define('SKIN', Session::get('tema'));
            }
        }
    }

    /**
     * Callback que se ejecuta después de los métodos de todos los controladores
     */
    final protected function finalize() {
        if(defined('APP_CLIENT')) {
            $this->page_title = trim($this->page_title).' | '.APP_CLIENT.' ‹ '.APP_NAME;
        } else {
            $this->page_title = trim($this->page_title).' ‹ '.APP_NAME;
        }
        //Se muestra la vista según el tipo de reporte
        if(Router::get('module') == 'reporte') {
            View::report($this->page_format);
        }
        //Se verifica si se cambia el título de la página
        if($this->set_title && Input::isAjax()) {
            $this->set_title = TRUE;
        }
    }

}

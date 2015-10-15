<?php

/**
 * Controller por defecto si no se usa el routes
 * 
 */
class IndexController extends BackendController {

   
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        $this->page_title = 'Procedimientos';
        //Se cambia el nombre del módulo actual        
        $this->page_module = 'Calidad';
    }

    public function index() {
        
    }

}

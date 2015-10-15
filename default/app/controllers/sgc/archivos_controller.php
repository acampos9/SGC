<?php
 
class ArchivosController extends BackendController{
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        $this->page_title = 'Subir documento';
        //Se cambia el nombre del módulo actual        
        $this->page_module = 'Formulario';
    }
    
    public function archivo() {
        //View::select('carga_documento');  //para mostrar siempre la vista con los formularios
        if (Input::hasPost('oculto')) {  //para saber si se envió el form
            $archivo = Upload::factory('archivo');//llamamos a la libreria y le pasamos el nombre del campo file del formulario
            $archivo->setExtensions(array('zip','rar')); //le asignamos las extensiones a permitir
            if ($archivo->isUploaded()) { 
                if ($archivo->save()) {
                    Flash::valid('Archivo subido correctamente...!!!');
                }
            }else{
                    Flash::warning('No se ha Podido Subir el Archivo...!!!');
            }
        }
    }
 
    public function imagen() {
        //View::select('carga_documento');  //para mostrar siempre la vista con los formularios
        if (Input::hasPost('oculto')) {  //para saber si se envió el form
 
            //llamamos a la libreria y le pasamos el nombre del campo file del formulario
            //el segundo parametro de Upload::factory indica que estamos subiendo una imagen
            //por defecto la libreria Upload trabaja con archivos...
            $archivo = Upload::factory('archivo', 'image'); 
            $archivo->setExtensions(array('jpg', 'png', 'gif'));//le asignamos las extensiones a permitir
            if ($archivo->isUploaded()) {
                if ($archivo->save()) {
                    Flash::valid('Imagen subida correctamente...!!!');
                }
            }else{
                    Flash::warning('No se ha Podido Subir la imagen...!!!');
            }
        }
    }
    
    /**
     * Método para subir archivos
     */
    public function upload() {     
        $upload = new DwUpload('archivo', 'files/sgc/');
        $upload->setAllowedTypes('pdf');
        $upload->setEncryptName(TRUE);
        //$upload->setSize(170, 200, TRUE);
        if(!$data = $upload->save()) { //retorna un array('path'=>'ruta', 'name'=>'nombre.ext');
            $data = array('error'=>$upload->getError());
        }
        sleep(1);//Por la velocidad del script no permite que se actualize el archivo
        View::json($data);
    }
 
}
?>

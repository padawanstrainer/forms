<?php 
declare( strict_types = 1 );
namespace Padawanstrainer\Forms;

class FormBuilder{
    private Array $valid_attrs_textarea = [
        'autofocus', 'cols', 'disabled', 'form', 'maxlength', 'placeholder', 'readonly', 'required', 'rows', 'wrap'
    ];
    private String $action;
    private String $method;
    private Array $form_options;
    private Array $form_fields = [];
    

    public function __construct(
        String $action, 
        String $method = 'POST', 
        Array $options = [ ]
    ){
        $this->action = $action;
        $this->method = $method;
        $this->form_options = $options;
    }

    public function addInput( String $name, String $type = 'text',  Array  $options = [ ] ): void{
        $opciones = $this->getAttributes($options);
        //agregue inputs, selects o textareas
        $this->form_fields[] = "<input type='{$type}' name='{$name}' {$opciones} />";
    }

    /* Crea una etiqueta select */
    public function addFascomp( String $name, Array $options, Array $attributes = [] ){
        $opcion_seleccionada = $attributes['selected'] ?? null;
        unset($attributes['selected']  );

        $opciones = $this->getAttributes($attributes);
        $select = "<select name='$name' $opciones>";
        foreach($options as $value => $text ){
            $checked = $opcion_seleccionada == $value ? ' selected' : '';
    
            $select.="<option value='{$value}'{$checked}>{$text}</option>";
        }
        $select.= "</select>";
        $this->form_fields[] = $select;
    }

    public function addTextarea( String $nombre, Array $attributes = [ ] ){
        if( ! isset($attributes['rows'] ) ) $attributes['rows'] = 8;
        if( ! isset($attributes['cols'] ) ) $attributes['cols'] = 50;
        $value = $attributes['value'] ?? '';

        $opciones = $this->getAttributes(
            $attributes, 
            $this->valid_attrs_textarea
        );
        $this->form_fields[] = "<textarea name='{$nombre}' {$opciones}>{$value}</textarea>";
    }

    /* Permite crear grupos de radio/checkbox */
    public function addFelixjet( String $name, Array $opciones, String $type = 'radio', Array $attributes = [] ){
        $opcion_seleccionada = $attributes['selected'] ?? null;
        unset($attributes['selected']  );

        $attrs = $this->getAttributes( $attributes );
        $hay_corchetes = $type == 'radio' ? '':'[]';
        $grupo = "<div {$attrs}>";
        foreach($opciones as $value => $text ){
            if( is_array( $opcion_seleccionada ) ){
                $checked = in_array($value, $opcion_seleccionada) ? 'checked' : '';
            }else{
                $checked = $value == $opcion_seleccionada ? 'checked': '';
            }
            $grupo .= "<label>{$text} <input type='{$type}' name='{$name}{$hay_corchetes}' value='{$value}' {$checked} /></label>";
        }
        $grupo .= "</div>";
        $this->form_fields[] = $grupo;
    }

    public function render( ): String{
        //var_dump($this->form_fields);
        //debería hacer el output del <form>
        $options = $this->getAttributes($this->form_options);

        $campos = implode( "<br>", $this->form_fields );
        return <<<FORM
<form method="{$this->method}" action="{$this->action}" {$options}>
    {$campos}
    <div><button type='submit'>Manda el fakin formulario que me cago de hambre y todavia no lo subiste a Composer</button></div>
</form>
FORM;

        /*
            OPCION 1
            <form method='POST' action='#' enctype='xx'>
                <div>
                    <label for='falopa'>Texto</label>
                    <input type="falopa" name="falopa" id="falopa" placeholder="falopa" />
                </div>
            </form>

            OPCION 2
            getField( 'nombre' ) => return <input type="falopa" name="falopa" id="falopa" />
        */
    }

    private function getAttributes( $array,$valid_attrs = []){
        $options = [];
        foreach( $array as $attr => $val ){
            if( ! empty($valid_attrs) && ! in_array($attr, $valid_attrs ) ){
                continue;
            }    
            $options[] = "$attr='$val'";
        }
        return implode( " ", $options );
    }
}
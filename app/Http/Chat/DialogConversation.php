<?php

namespace App\Http\Chat;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Models\TipoIncidencia;
use App\Models\User;
use App\Models\Incidencia;
use App\Models\SeguimientoIncidencia;

use Haruncpi\LaravelIdGenerator\IdGenerator;

class DialogConversation extends Conversation
{
    protected $id_user;
    protected $dni;
    protected $name;
    protected $email;
    protected $phone;
    protected $message;
    protected $idtipoincidencia;
    protected $idprioridad;
    protected $lang;
    protected $validacion_usuario;



    public function run()
    {

        $this->seleccionar_lenguaje();
    }

    public function seleccionar_lenguaje()
    {
        $botman = resolve('botman');
        $botman->hears('{message}', function($botman, $message){
            if ( in_array($message, array("Hola","HOLA","hola", "hi", "hello","que tal","buenos dias","buenas tardes"))) {
                // $botman->typesAndWaits(2);
                $question =  Question::create('¿En qué idioma quieres seguir?')
                ->fallback('Debe elegir una opción correcta...')
                ->callbackId('que_quieres_hacer')
                ->addButtons([
                    Button::create('ESPAÑOL')->value('es')->name('Español'),
                    Button::create('INGLES')->value('en')->name('Quechua'),
                ]);
                $this->ask($question, function(Answer $answer) use($botman) {
                    if ($answer->isInteractiveMessageReply()){
                        $value = $answer->getValue();
                        $text = $answer->getText();
                        if (in_array($value,['es','en'])) {
                            $this->lang=$value;
                            // $this->say('Elegiste el idioma '.language_text($text));
                            $this->preguntar_dni();
                        }
                    }
                });
            }else if(in_array($message, array("adiós", "chau", "finalizar","end"))){
                $botman->reply("Gracias por usar nuestro chatboot");
            }else{
                // $botman->reply("Hola, ");
                $this->lang='es';
                $this->preguntar_dni();
            }

        });
    }
    public function preguntar_dni(){
        switch ($this->lang) {
            case 'es':
                # code...
            $this->ask(Question::create('Ingrese su DNI'), function(Answer $answer) {
                $dni = $answer->getText();
                if(strlen($dni)!=8){
                    $this->say("Ingrese un DNI Valido");
                    $this->preguntar_dni();
                }else{
                    $val_dni=User::where('dni',$dni)->first();
                    if ($val_dni) {
                        $this->say("Bienvenido {$val_dni->name}");
                        $this->id_user=$val_dni->id;
                        $this->dni=$val_dni->dni;
                        $this->name=$val_dni->name;
                        $this->email=$val_dni->email;
                        $this->phone=$val_dni->telefono;
                        $this->validacion_usuario="1";
                        $this->askActions();
                    }else{
                        $this->dni=$dni;
                        $this->validacion_usuario="0";
                        $this->preguntar_nombre();
                    }

                    
                }
            });
            break;
            case 'en':
                # code...
            $this->ask(Question::create('¿What is you DNI?'), function(Answer $answer) {
                $name = $answer->getText();
                $this->name=$name;
                $this->say("Allipaqmi shamurquyki {$name}");
                $this->askActions();
                $this->seleccionar_lenguaje();
            });

            break;

            default:
                # code...
            break;
        }
    }
    public function preguntar_nombre(){
        switch ($this->lang) {
            case 'es':
                # code...
            $this->ask(Question::create('¿Cuál es tu nombre?'), function(Answer $answer) {
                $name = $answer->getText();
                $this->name=$name;
                $this->say("Bienvenido {$name}");
                // $this->askActions();
                $this->askContactPhone();
            });
            break;

            case 'en':
            
            break;

            default:
            
            break;
        }

    }
    public function askContactPhone(){
        switch ($this->lang) {
            case 'es':
                # code...
            $this->ask(Question::create('Ingrese su número de teléfono'), function(Answer $answer) {
                $text = $answer->getText();
                if ($text) {
                   if(strlen($text)!=9){
                    $this->say("Ingrese Numero de celular valido");
                    $this->askContactPhone();
                }else{
                    $this->phone=$text;
                    $this->askContactEmail();
                }
            }else{
                $this->askContactPhone();
            }
        });
            break;

            case 'en':
                # code...
            $this->ask(Question::create('Churay telefunuykipa numirunta.'), function(Answer $answer) {
                $text = $answer->getText();
                if ($text) {
                    if(strlen($text)!=9){
                        $this->say("Ingrese Numero de celular valido");
                        $this->askContactPhone();
                    }else{
                        $this->phone=$text;
                        $this->askContactEmail();

                    }
                }else{
                    $this->askContactPhone();
                }
            });
            break;

            default:
                # code...
            break;
        }
    }
    public function askContactEmail(){
        switch ($this->lang) {
            case 'es':
                # code...
            $this->ask(Question::create('Ingrese su correo'), function(Answer $answer) {
                $text = $answer->getText();
                if ($text) {
                    if (!filter_var($text, FILTER_VALIDATE_EMAIL)) {
                        $this->say("Ingrese Correo Valido valido");
                        $this->askContactEmail();
                    }
                    else{
                        $val_email=User::where('email',$text)->first();
                        if ($val_email) {
                            $this->say("El correo ya esta registrado ingrese uno diferente");
                            $this->askContactEmail();
                        }else{
                            $this->email=$text;
                            $this->askActions();
                        }

                    }

                }else{
                    $this->askContactEmail();
                }
            });
            break;

            case 'en':

            break;

            default:
                # code...
            break;
        }
    }
    public function askActions(){
        switch ($this->lang) {
            case 'es':
                # code...
            $question = Question::create('Elige la acción que deseas realizar')
            ->callbackId('action')
            ->addButtons([
                Button::create('Información de los servicios')->value('product_info'),
                Button::create('Seguimiento de Incidencias')->value('seguimiento_pedido'),
                Button::create('Contactar con un asesor / Registrar Incidencia')->value('contact'),
                    // Button::create('Otras consultas')->value('other'),
            ]);
            $this->ask($question, function(Answer $answer) {
                if ($answer->isInteractiveMessageReply()){
                    $value = $answer->getValue();
                    $text = $answer->getText();

                    switch ($value) {
                        case 'product_info':
                         $this->say("Para informacion contactase a los siguientes numeros 992858654 / 921510458");
                        // $this->listAllResults();
                        break;

                        case 'seguimiento_pedido':
                                # code...
                        $this->askSeguimientoPedido();
                        break;
                        case 'contact':
                                # code...
                        $this->seleccionar_tipo_incidencia();
                        break;

                        case 'other':
                                # code...
                        break;


                        default:
                                # code...
                        break;
                    }
                }
            });
            break;

            case 'en':
                # code...
            $question = Question::create('Ashii imata rurayta munanqaykita')
            ->callbackId('action')
            ->addButtons([
                Button::create('Iyachaykuna hina imanaw pruduktukunata ishinii')->value('product_info'),
                Button::create('Mañakunqaykita qatii')->value('seguimiento_pedido'),
                Button::create('Rimakurii huk yachatsikuqwan')->value('contact'),
                    // Button::create('Otras consultas')->value('other'),
            ]);
            $this->ask($question, function(Answer $answer) {
                if ($answer->isInteractiveMessageReply()){
                    $value = $answer->getValue();
                    $text = $answer->getText();

                    switch ($value) {
                        case 'product_info':
                                # code...
                        $this->listAllResults();
                        break;

                        case 'seguimiento_pedido':
                                # code...
                        $this->askSeguimientoPedido();
                        break;
                        case 'contact':
                                # code...
                                // $this->askContactPhone();
                        $this->seleccionar_tipo_incidencia();
                        break;

                        case 'other':
                                # code...
                        break;


                        default:
                                # code...
                        break;
                    }
                }
            });
            break;

            default:
                # code...
            break;
        }
    }
    public function seleccionar_tipo_incidencia()
    {
       $ti1=TipoIncidencia::where('id',1)->first();
       $ti2=TipoIncidencia::where('id',2)->first();
       $ti3=TipoIncidencia::where('id',3)->first();

       switch ($this->lang) {
        case 'es':
                # code...
        $question = Question::create('Seleccione tipo incidencia')
        ->callbackId('action')
        ->addButtons([   
            Button::create(''.$ti1->nombre.'')->value(''.$ti1->id.''),                                    

            Button::create(''.$ti2->nombre.'')->value(''.$ti2->id.''),                                    

            Button::create(''.$ti3->nombre.'')->value(''.$ti3->id.''),                                    
        ]);
        $this->ask($question, function(Answer $answer) {
            if ($answer->isInteractiveMessageReply()){
                $value = $answer->getValue();
                $this->idtipoincidencia=$value;
                $this->seleccionar_prioridad();
            }
        });
        break;

        case 'en':

        break;

        default:
                # code...
        break;
    }
}
public function seleccionar_prioridad()
{

    switch ($this->lang) {
        case 'es':
                # code...
        $question = Question::create('Seleccione prioridad de la Incidencias')
        ->callbackId('action')
        ->addButtons([   
            Button::create('ALTA')->value('1'), 
            Button::create('MEDIA')->value('2'),                                    
            Button::create('BAJA')->value('3'),                                                             
        ]);
        $this->ask($question, function(Answer $answer) {
            if ($answer->isInteractiveMessageReply()){
                $value = $answer->getValue();
                $this->idprioridad=$value;
                $this->askContactMessage();
            }
        });
        break;

        case 'en':

        break;

        default:
                # code...
        break;
    }
}

public function askContactMessage(){
    switch ($this->lang) {
        case 'es':
                # code...
        $this->ask(Question::create('Ingrese la descripcion de su problema posteriormente un asesor se contactará lo mas pronto contigo.'), function(Answer $answer) {
            $text = $answer->getText();
            if ($text) {
                $this->message=$text;
                $codigo = IdGenerator::generate(['table' => 'incidencia','field'=>'codigo', 'length' => 10, 'prefix' =>'INC-']);
                if ($this->validacion_usuario == "0") {
                    $password = bcrypt($this->dni);
                $u=User::create([
                    'tipo_usuario'=>"3",
                    'area'=>"1",
                    'dni'=>$this->dni,
                    'name'=>$this->name,
                    'telefono'=>$this->phone,
                    'email'=>$this->email,
                    'password'=>$password,
                ]);
                $in=Incidencia::create([
                    'id_tipo_incidencia'=>$this->idtipoincidencia,
                    'codigo'=>$codigo,
                    'id_prioridad'=>$this->idprioridad,
                    'descripcion'=>$this->message,
                    'id_creador'=>$u->id,
                    'id_estado'=>"1",
                ]);
                $sin=SeguimientoIncidencia::create([
                    'id_incidencia'=>$in->id,
                    'id_accion'=> '1',
                    'emisor' => $u->id,
                    'detalle'=>"La incidencia fue registrada exitosamente",
                    'documento'=>"",
                ]);
                }elseif($this->validacion_usuario == "1"){
                    $in=Incidencia::create([
                    'id_tipo_incidencia'=>$this->idtipoincidencia,
                    'codigo'=>$codigo,
                    'id_prioridad'=>$this->idprioridad,
                    'descripcion'=>$this->message,
                    'id_creador'=>$this->id_user,
                    'id_estado'=>"1",
                ]);
                $sin=SeguimientoIncidencia::create([
                    'id_incidencia'=>$in->id,
                    'id_accion'=> '1',
                    'emisor' => $this->id_user,
                    'detalle'=>"La incidencia fue registrada exitosamente",
                    'documento'=>"",
                ]);
                }

                $this->say('Gracias por enviar tu información, un asesor se contactará contigo.');
                $this->say("Elige otra acción que deseas realizar");
                $this->askActions();
            }else{
                $this->askContactMessage();
            }

        });
        break;

        case 'en':

        break;

        default: 
                # code...
        break;
    }
}

public function crear_usuario(){
    $password = bcrypt($this->dni);
    User::create([
        'tipo_usuario'=>"3",
        'area'=>"1",
        'dni'=>$this->dni,
        'name'=>$this->name,
        'email'=>$this->email,
        'dni'=>$password,
    ]);
}
}

<section class="content">
<div class="row">
  <div class="col-md-12">
 <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
               <h1 class="m-0"><i class='fa fa-globe'></i> Editar Pagina Web</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class='fa fa-home'></i> Tablero</li>
              <li class="breadcrumb-item active"><i class='fa fa-globe'></i> Pagina Web</li>
            </ol>
          </div><!-- /.col -->
    </div>
 <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-warning card-outline">
              <div class="card-body box-profile" style="background-color:#222;">
                <div class="text-center">
                  <img class="profile-user-img img-fluid "
                       src="CF-SYSTEMS/storage/configuration/<?php echo StockData::getPrincipal()->ticket_image;?>"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo StockData::getPrincipal()->name;?></h3>
                

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item" style="background-color:#222;">
                    <b class="text-warning">Instagram:</b>
                  </li>
                  <li class="list-group-item" style="background-color:#222;">
                    <b><?php echo StockData::getPrincipal()->field2;?></b> 
                  </li>
                  <li class="list-group-item" style="background-color:#222;">
                    <b class="text-warning">Correo:</b> 
                  </li>
                  <li class="list-group-item" style="background-color:#222;">
                    <b><?php echo StockData::getPrincipal()->email;?></b> 
                  </li>
                </ul>

                <a href="https://<?php echo StockData::getPrincipal()->web_url2;?>" class="btn btn-primary btn-block"><b>VER PAGINA WEB</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">Acerca de mí</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body" style="background-color:#222;">
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Direccion</strong>

                <p class="text-muted"><?php echo StockData::getPrincipal()->address;?></p>

                <hr>

                <strong><i class="fas fa-phone-alt mr-1"></i> Telefono</strong>

                <p class="text-muted"><?php echo StockData::getPrincipal()->phone;?></p>
                
                 <strong><i class="fab fa-whatsapp mr-1"></i> WhatsApp</strong>

                <p class="text-muted"><?php echo StockData::getPrincipal()->phone2;?></p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card" style="background-color:#222;">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">INICIO</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">SERVICIOS</a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">VEHICULOS</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                    
                 <div class="timeline timeline-inverse">
                      <!-- timeline time label -->
                   
                      <!-- timeline item -->
                      <div>
                        <i class="fa fa-bullseye bg-primary"></i>

                        <div class="timeline-item" style="background-color:#222;">
                        

                          <h3 class="timeline-header text-warning">PORTADA</h3>

                          <div class="timeline-body">
                              
                           <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Portada Web:</label>
      <input style="background-color:#222;"  type="file" name="portada_img" >
    </div>
    
    
                   <div class="col-md-12 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Texto de Portada:</label>
     <input style="background-color:#333; color: white;"  type="text" name="portada_text" value="¡Viaja seguro! Elige tu vehiculo ideal y disfruta de un servicio de primera clase." >
    </div>


    <div class="col-md-6 col-12">
   <?php if(isset($stock->web_img)):?>
  <img src="WEB/img/<?php echo $stock->web_img;?>" style="width:20%;">
       <?php endif;?>
  </div>

                          </div>
                          <div class="timeline-footer">
                            <a href="#" class="btn btn-primary btn-sm btn-block">Actualizar Portada</a>
                           
                          </div>
                        </div>
                      </div>
                      <!-- END timeline item -->
                      <!-- timeline item -->
                      <div>
                        <i class="fa fa-bullseye bg-info"></i>

                        <div class="timeline-item" style="background-color:#222;">

                          <h3 class="timeline-header border-0 text-warning"> CARACTERISTICA
                          </h3>
                        </div>
                      </div>
                      <!-- END timeline item -->
                      <!-- timeline item -->
                      <div>
                        <i class="fa fa-bullseye bg-warning"></i>

                        <div class="timeline-item" style="background-color:#222;">

                           <div class="col-md-12 col-12 my-2">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Texto del Encabezado:</label>

                            <textarea style="background-color:#333; color: white; width:100%;" rows="3" style="background-color:#333; color: white;"  type="text" name="portada_text" >En esta sección, resaltamos las características que nos definen como empresa de alquiler de coches. Estas características muestran nuestras fortalezas, diferenciandonos de la competencia.</textarea>
    </div>
    <div class="col-md-12 col-12 my-2">
 <label for="inputEmail1" class="col-md-12 col-12 control-label">Texto de Seccion:</label>
 
                            <input style="background-color:#333; width:100%;" rows="2" class="text-warning"  type="text" name="portada_text" value="Servicios de primera clase">
                            
                            <textarea style="background-color:#333; color: white; width:100%;" rows="2" class="my-2"  type="text" name="portada_text" >Nos enorgullece ofrecer una experiencia de alquiler de vehículos que supera todas las expectativas.</textarea>
                            
                            <br/>
                             <input style="background-color:#333; width:100%;" rows="2" class="text-warning"  type="text" name="portada_text" value="Asistencia en carretera 24 horas al día, 7 días a la semana">
                             
                            <textarea style="background-color:#333; color: white; width:100%;" rows="3" class="my-2"  type="text" name="portada_text" >Es un componente crucial para brindar una experiencia de alquiler de vehículos de alta calidad. Este servicio garantiza que los clientes puedan recibir ayuda en cualquier momento y en cualquier lugar durante el período de alquiler.</textarea>
                             
                            <br/>
                             <input style="background-color:#333;  width:100%;" rows="2" class="text-warning"  type="text" name="portada_text" value="Calidad al mínimo">
                             
                            <textarea style="background-color:#333; color: white; width:100%;" rows="2" class="my-2"  type="text" name="portada_text" >Una estrategia para atraer a clientes que buscan opciones económicas sin los lujos de un servicio premium.</textarea>
                             
                            <br/>
                              <input style="background-color:#333; width:100%;" rows="2" class="text-warning"  type="text" name="portada_text" value="Recogida y devolución">
                              
                            <textarea style="background-color:#333; color: white; width:100%;" rows="3" class="my-2"  type="text" name="portada_text">Este servicio implica que el cliente puede solicitar que el coche de alquiler sea recogido en una ubicación específica con un costo adicional. Esto puede ser desde su hogar, oficina, o cualquier otro lugar acordado..</textarea>
    </div>
    
     <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Foto Central (PNG):</label>
      <input style="background-color:#222;"  type="file" name="portada_img" >
    </div>
                         <div class="timeline-footer">
                            <a href="#" class="btn btn-primary btn-sm btn-block">Actualizar Características</a>
                           
                          </div>
                        </div>
                      </div>
                      <!-- END timeline item -->
                      
                       <div>
                        <i class="fa fa-bullseye bg-info"></i>

                        <div class="timeline-item" style="background-color:#222;">

                          <h3 class="timeline-header border-0 text-warning"> ACERCA DE
                          </h3>
                        </div>
                      </div>
                      
                      <!-- timeline item -->
                             <div>
                        <i class="fa fa-bullseye bg-warning"></i>

                        <div class="timeline-item" style="background-color:#222;">

                           <div class="col-md-12 col-12 my-2">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Texto del Encabezado:</label>

                            <textarea style="background-color:#333; color: white; width:100%;" rows="3"  type="text" name="portada_text">En <?php echo StockData::getPrincipal()->name;?>, nos dedicamos a ofrecerte una experiencia de alquiler de coches que combina comodidad, confiabilidad y accesibilidad. Somos tu compañero de confianza para todos tus viajes, ya sea por negocios, placer o cualquier necesidad de transporte temporal.</textarea>
    </div>
    
    
                  <div class="col-md-12 col-12 my-2">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nuestra Mision:</label>

                            <textarea style="background-color:#333; color: white; width:100%;" rows="9"  type="text" name="portada_text">Ser la opción de alquiler de coches preferida por los clientes, ofreciendo experiencias excepcionales a través de un servicio confiable, accesible y personalizado.

Enfoque: Centrada en la satisfacción del cliente.

Objetivo: Crear experiencias memorables y positivas para cada cliente.

Valor: Fiabilidad, accesibilidad, personalización</textarea>
    </div>
    
     <div class="col-md-12 col-12 my-2">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Nuestra Vision:</label>

                            <textarea style="background-color:#333; color: white; width:100%;" rows="9"  type="text" name="portada_text">Ofrecer soluciones de movilidad confiables y accesibles para nuestros clientes, brindando un servicio excepcional que supere sus expectativas en cada viaje.

Enfoque: Atención al cliente.

Objetivo: Brindar un servicio que cumpla y supere las expectativas de los clientes.

Valores: Confianza, Accesibilidad, Excepcionalidad.</textarea>
    </div>
    
    
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Foto Vertical (JPG):</label>
      <input style="background-color:#222;"  type="file" name="portada_img" >
    </div>
    
    
    <div class="col-md-6 col-12">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Foto Horizontal (JPG):</label>
      <input style="background-color:#222;"  type="file" name="portada_img" >
    </div>
    
              <div class="col-md-12 col-12 my-2">
    <label for="inputEmail1" class="col-md-12 col-12 control-label">Texto del 2do Encabezado:</label>

                            <textarea style="background-color:#333; color: white; width:100%;" rows="2" style="background-color:#333; color: white;"  type="text" name="portada_text" >Nuestros vehículos son revisados regularmente para asegurar que siempre estén en perfectas condiciones.</textarea>
                            
                            
                            <input style="background-color:#333; width:100%;" rows="2" class="text-warning"  type="text" name="portada_text" value="Flota Moderna y Bien Mantenida">
                            
                             <input style="background-color:#333; width:100%;" rows="2" class="text-warning"  type="text" name="portada_text" value="Precios Competitivos y Ofertas Especiales">
                             
                             <input style="background-color:#333; width:100%;" rows="2" class="text-warning" type="text" name="portada_text" value="Atención al Cliente Dedicada">
                             
                              <input style="background-color:#333; width:100%;" rows="2" class="text-warning"  type="text" name="portada_text" value="Proceso de Reserva Rápido y Sencillo">
    </div>
                         <div class="timeline-footer">
                            <a href="#" class="btn btn-primary btn-sm btn-block">Actualizar Acerca De</a>
                           
                          </div>
                        </div>
                      </div>
                      <!-- END timeline item -->
                      <!-- END timeline item -->
                      <div>
                        <i class="fa fa-bullseye bg-gray"></i>
                      </div>
                    </div>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                 SERVICIOS        
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="settings">
                    <form class="form-horizontal">
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="inputName" placeholder="Name">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputName2" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputName2" placeholder="Name">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputExperience" class="col-sm-2 col-form-label">Experience</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputSkills" class="col-sm-2 col-form-label">Skills</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-danger">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
      </div>
    </section>
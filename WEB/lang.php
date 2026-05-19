<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ----------------------------------------------------------------------
 * Language selection
 * --------------------------------------------------------------------
 * Accept the override from ?lang= case-insensitively. Some other parts
 * of the system (admin panel) write Core::$user->language as 'ES' / 'EN'
 * which, before this normalization, could leak into $_SESSION['lang']
 * and cause every __() call to fall through to the bare key (since the
 * translation arrays are keyed lowercase). We now always normalize and
 * always validate against the available languages so __() can never end
 * up returning "nav_home" instead of the translated label.
 * -------------------------------------------------------------------- */
$AVAILABLE_LANGS = ['es', 'en'];
$DEFAULT_LANG    = 'es';

if (isset($_GET['lang'])) {
    $g = strtolower(trim((string)$_GET['lang']));
    if (in_array($g, $AVAILABLE_LANGS, true)) {
        $_SESSION['lang'] = $g;
    }
}

if (isset($_SESSION['lang']) && is_string($_SESSION['lang'])) {
    $_SESSION['lang'] = strtolower(trim($_SESSION['lang']));
}
if (!isset($_SESSION['lang']) || !in_array($_SESSION['lang'], $AVAILABLE_LANGS, true)) {
    $_SESSION['lang'] = $DEFAULT_LANG;
}

$LANG = $_SESSION['lang'];
$GLOBALS['LANG'] = $LANG;

$translations = [
    'es' => [
        'nav_home'        => 'INICIO',
        'nav_cars'        => 'VEHÍCULOS',
        'nav_about'       => 'NOSOTROS',
        'nav_services'    => 'SERVICIOS',
        'nav_contact'     => 'CONTACTO',
        'nav_privacy'     => 'PRIVACIDAD',
        'privacy_title'           => 'Política de Privacidad',
        'privacy_updated'         => 'Última actualización',
        'privacy_intro'           => 'Solutions Rent Car ("nosotros") opera la aplicación móvil Solutions Rent Car y el sitio web solutionsrentcar.do. Esta política describe qué datos recopilamos, cómo los usamos y los derechos que tienes sobre ellos.',
        'privacy_h_collect'       => '1. Información que recopilamos',
        'privacy_collect_account' => '<strong>Datos de cuenta:</strong> nombre, teléfono, correo electrónico, número de identificación (cédula/pasaporte) y datos de licencia de conducir.',
        'privacy_collect_booking' => '<strong>Datos de reserva:</strong> fechas, vehículo seleccionado, lugar de retiro/entrega y método de pago.',
        'privacy_collect_docs'    => '<strong>Documentos:</strong> fotos de cédula, licencia y firmas digitales que subes para validar reservas.',
        'privacy_collect_device'  => '<strong>Datos del dispositivo:</strong> modelo, sistema operativo y token de notificaciones push para enviarte avisos de tus reservas y pagos.',
        'privacy_h_use'           => '2. Cómo usamos tus datos',
        'privacy_use_1'           => 'Procesar y administrar tus reservas de alquiler de vehículos.',
        'privacy_use_2'           => 'Verificar tu identidad y elegibilidad para conducir.',
        'privacy_use_3'           => 'Enviarte notificaciones sobre el estado de tu reserva, pagos y recordatorios.',
        'privacy_use_4'           => 'Cumplir con obligaciones contables, fiscales y legales en República Dominicana.',
        'privacy_h_share'         => '3. Compartir información',
        'privacy_share_text'      => 'No vendemos tus datos. Solo los compartimos con proveedores estrictamente necesarios para operar el servicio (procesador de pagos, hosting) y cuando lo exija la ley.',
        'privacy_h_storage'       => '4. Almacenamiento y seguridad',
        'privacy_storage_text'    => 'Tus datos se almacenan en servidores de Hostinger con conexiones cifradas (HTTPS). Aplicamos controles de acceso y respaldos periódicos.',
        'privacy_h_rights'        => '5. Tus derechos',
        'privacy_rights_text'     => 'Puedes solicitar acceso, corrección o eliminación de tus datos enviando un correo a [email]. Responderemos en un plazo máximo de 30 días.',
        'privacy_h_push'          => '6. Notificaciones push',
        'privacy_push_text'       => 'Las notificaciones se envían usando los servicios oficiales de Apple (APNs) y Google (Firebase Cloud Messaging). Puedes desactivarlas desde los ajustes de tu dispositivo en cualquier momento.',
        'privacy_h_children'      => '7. Niños',
        'privacy_children_text'   => 'El servicio está dirigido a personas mayores de 21 años, edad mínima legal para alquilar vehículos en República Dominicana. No recopilamos intencionalmente datos de menores.',
        'privacy_h_changes'       => '8. Cambios',
        'privacy_changes_text'    => 'Cualquier cambio relevante se anunciará en esta página y, cuando proceda, mediante notificación dentro de la app.',
        'privacy_h_contact'       => '9. Contacto',
        'privacy_email'           => 'Correo',
        'book_a_car'      => 'Reservar Vehículo',
        'learn_more'      => 'Saber Más',
        'date_from'       => 'Fecha desde:',
        'date_to'         => 'Fecha hasta:',
        'search'          => 'Buscar:',
        'premium_fleet'   => 'Flota Premium',
        'explore_cars'    => 'Explora Nuestra Flota',
        'explore_sub'     => 'Elige de nuestra exclusiva colección de vehículos premium diseñados para comodidad, lujo y rendimiento.',
        'available_now'   => 'Disponible Ahora',
        'car_desc_default'=> 'Vehículo premium con comodidad, rendimiento y una experiencia exclusiva de conducción.',
        'per_day'         => '/ Día',
        'rent_now'        => 'Reservar Ahora',
        'no_cars'         => 'No hay vehículos disponibles para este rango de fechas.',
        'invalid_dates'   => 'Fechas inválidas.',
        'date_order_err'  => 'La fecha de inicio no puede ser mayor que la fecha de fin.',
        'from_label'      => 'Desde',
        'to_label'        => 'Hasta',
        'check_avail'     => 'Verificar Disponibilidad',
        'book_now'        => 'Reservar Ahora',
        'no_cars_avail'   => 'No hay vehículos disponibles',
        'no_cars_msg'     => 'En este momento no hay vehículos de alquiler disponibles.',
        'seats'           => 'Asientos',
        'js_select_dates' => 'Por favor seleccione fecha desde y fecha hasta.',
        'js_date_order'   => 'La fecha final no puede ser menor que la inicial.',
        'js_min_days'     => 'La reserva mínima es de 3 días.',
        'js_available'    => '✓ Disponible para esas fechas.',
        'js_unavailable'  => '✗ No disponible para esas fechas.',
        'hero_mini'       => 'Servicio Premium de Alquiler',
        'hero_title_1'    => 'Experiencia',
        'hero_title_2'    => 'Premium',
        'hero_title_3'    => 'Alquiler de Autos',
        'hero_title_4'    => 'Para Cada Viaje',
        'hero_sub'        => 'Vehículos de lujo, servicio premium y experiencias de conducción inolvidables. Reserva tu auto ideal con la empresa de alquiler más confiable.',
        'about_mini'      => 'Sobre Nosotros',
        'about_who'       => 'Quiénes Somos',
        'about_title'     => 'Tu Empresa de Alquiler de Confianza',
        'about_p1'        => 'En [nombre], creemos que alquilar un vehículo debe ser simple, elegante y confiable. Nuestro objetivo es ofrecer a cada cliente una experiencia premium desde el momento en que eligen un vehículo hasta el final de su viaje.',
        'about_p2'        => 'Ya sea que necesite un auto para negocios, vacaciones, uso personal o una ocasión especial, ofrecemos vehículos cómodos, limpios y bien mantenidos listos para la carretera.',
        'about_p3'        => 'Nos enfocamos en la calidad del servicio, transparencia y satisfacción del cliente, haciendo que cada experiencia de alquiler sea fluida y profesional.',
        'fleet_title'     => 'Flota Premium',
        'fleet_desc'      => 'Vehículos modernos seleccionados para ofrecer comodidad, rendimiento y estilo para todo tipo de viaje.',
        'safe_title'      => 'Seguro y Confiable',
        'safe_desc'       => 'Vehículos inspeccionados y mantenidos para brindar confianza, seguridad y tranquilidad.',
        'support_title'   => 'Soporte Profesional',
        'support_desc'    => 'Asistencia amigable y comunicación clara antes, durante y después de su alquiler.',
        'quality'         => 'Calidad de Servicio',
        'support'         => 'Soporte',
        'clean'           => 'Limpio',
        'vehicles'        => 'Vehículos',
        'fast'            => 'Rápido',
        'booking_stat'    => 'Reservas',
        'cta_title'       => '¿Listo para reservar su vehículo?',
        'cta_sub'         => 'Elige tu vehículo hoy y disfruta de una experiencia premium de alquiler con comodidad, confianza y servicio profesional.',
        'view_cars'       => 'Ver Nuestros Autos',
        'services_mini'   => 'Nuestros Servicios',
        'services_title'  => 'Servicios Premium de Alquiler',
        'services_sub'    => 'Ofrecemos soluciones flexibles de alquiler para viajes diarios, viajes de negocios, recogida en aeropuerto y ocasiones especiales.',
        'what_we_offer'   => 'Lo Que Ofrecemos',
        'services_built'  => 'Servicios Diseñados Para Su Comodidad',
        'daily_title'     => 'Alquiler Diario',
        'daily_desc'      => 'Alquile un vehículo por un día o varios días con un proceso rápido, simple y profesional.',
        'weekly_title'    => 'Alquiler Semanal',
        'weekly_desc'     => 'Planes de alquiler semanal flexibles diseñados para clientes que necesitan comodidad y mejores precios.',
        'longterm_title'  => 'Alquiler a Largo Plazo',
        'longterm_desc'   => 'Opciones de alquiler mensual o a largo plazo para empresas, uso personal y estadías prolongadas.',
        'airport_title'   => 'Recogida en Aeropuerto',
        'airport_desc'    => 'Servicio de entrega y recogida de vehículos para viajeros que necesitan una llegada sin problemas.',
        'safeveh_title'   => 'Vehículos Seguros',
        'safeveh_desc'    => 'Vehículos limpios, inspeccionados y bien mantenidos preparados para brindarle seguridad y confianza.',
        'support2_title'  => 'Soporte al Cliente',
        'support2_desc'   => 'Soporte profesional antes, durante y después de su alquiler para mejorar su experiencia.',
        'how_it_works'    => 'Cómo Funciona',
        'easy_process'    => 'Proceso de Alquiler Sencillo',
        'step1_title'     => 'Elige tu Auto',
        'step1_desc'      => 'Seleccione el vehículo que se adapte a su viaje, estilo y presupuesto.',
        'step2_title'     => 'Envía tu Solicitud',
        'step2_desc'      => 'Contáctenos o haga su solicitud de reserva rápidamente en línea.',
        'step3_title'     => 'Confirma los Detalles',
        'step3_desc'      => 'Confirmamos fechas, lugar de recogida, documentos y condiciones del alquiler.',
        'step4_title'     => 'Disfruta tu Viaje',
        'step4_desc'      => 'Reciba su vehículo y disfrute de una experiencia de alquiler segura y cómoda.',
        'need_vehicle'    => '¿Necesita un Vehículo Hoy?',
        'need_sub'        => 'Explore nuestra flota disponible y reserve el auto indicado para su próximo viaje con servicio premium y total confianza.',
        'view_cars2'      => 'Ver Autos',
        'contact_title'   => 'Contáctenos',
        'contact_sub'     => 'Estamos aquí para ayudarle con su próxima experiencia de alquiler.',
        'send_message'    => 'Envíenos un Mensaje',
        'send_sub'        => 'Complete el formulario y nuestro equipo le contactará pronto.',
        'full_name'       => 'Nombre Completo',
        'email_addr'      => 'Correo Electrónico',
        'phone_num'       => 'Número de Teléfono',
        'subject'         => 'Asunto',
        'message'         => 'Mensaje',
        'send_btn'        => 'Enviar Mensaje',
        'contact_info'    => 'Información de Contacto',
        'address'         => 'Dirección',
        'email'           => 'Correo',
        'phone'           => 'Teléfono',
        'hours'           => 'Horario de Atención',
        'hours_val'       => 'Lunes - Sábado',
        'all_rights'      => 'Todos los derechos reservados.',
        'footer_company'  => 'Empresa',
        'footer_services' => 'Servicios',
        'footer_contact'  => 'Contacto',
        'footer_home'     => 'Inicio',
        'footer_cars'     => 'Vehículos',
        'footer_about'    => 'Nosotros',
        'footer_luxury'   => 'Alquiler de Lujo',
        'footer_airport'  => 'Recogida Aeropuerto',
        'footer_daily'    => 'Alquiler Diario',
        'footer_weekly'   => 'Alquiler Semanal',
        'footer_longterm' => 'Largo Plazo',
        'footer_text'     => 'Servicio premium de alquiler de autos diseñado para comodidad, lujo y confianza. Ofrecemos vehículos modernos, soporte profesional y una experiencia fluida en cada viaje.',
        'cars_hero_mini'  => 'Nuestra Flota',
        'cars_hero_title' => 'Elige Tu Auto Perfecto',
        'cars_hero_sub'   => 'Explora nuestra flota premium de vehículos disponibles para alquiler.',
        'our_fleet'       => 'Nuestra Flota',
        'choose_car'      => 'Elige Tu Vehículo Perfecto',
        'premium_fleet2'  => 'Flota Premium de Alquiler',
        'available'       => 'Disponible',
        'per_day2'        => 'Por Día',
        'rent_a_car'      => 'ALQUILER DE AUTOS',
        'notif_title'              => 'Notificaciones',
        'notif_preferences'        => 'Preferencias',
        'notif_mark_all'           => 'Marcar todas como leídas',
        'notif_mark_read'          => 'Marcar como leída',
        'notif_empty'              => 'No tienes notificaciones aún.',
        'notif_new'                => 'NUEVO',
        'notif_event'              => 'Evento',
        'notif_inapp'              => 'En la app',
        'notif_email'              => 'Correo',
        'notif_save'               => 'Guardar',
        'notif_back'               => 'Volver',
        'notif_ev_booking_web'     => 'Reserva creada (web)',
        'notif_ev_booking_created' => 'Reserva creada (oficina)',
        'notif_ev_booking_delivered'=> 'Vehículo entregado',
        'notif_ev_booking_canceled'=> 'Reserva cancelada',
        'notif_ev_payment_received'=> 'Pago recibido',
        'notif_ev_reminder_return' => 'Recordatorio de devolución',
        'notif_ev_reminder_pickup' => 'Recordatorio de entrega',
    ],
    'en' => [
        'nav_home'        => 'HOME',
        'nav_cars'        => 'CARS',
        'nav_about'       => 'ABOUT',
        'nav_services'    => 'SERVICES',
        'nav_contact'     => 'CONTACT',
        'nav_privacy'     => 'PRIVACY',
        'privacy_title'           => 'Privacy Policy',
        'privacy_updated'         => 'Last updated',
        'privacy_intro'           => 'Solutions Rent Car ("we") operates the Solutions Rent Car mobile app and the website solutionsrentcar.do. This policy describes what data we collect, how we use it, and the rights you have over it.',
        'privacy_h_collect'       => '1. Information we collect',
        'privacy_collect_account' => '<strong>Account data:</strong> name, phone, email, ID number (cedula/passport) and driver license details.',
        'privacy_collect_booking' => '<strong>Booking data:</strong> dates, selected vehicle, pickup/return location and payment method.',
        'privacy_collect_docs'    => '<strong>Documents:</strong> photos of ID, license and digital signatures you upload to validate reservations.',
        'privacy_collect_device'  => '<strong>Device data:</strong> model, operating system and push notification token to send you alerts about your reservations and payments.',
        'privacy_h_use'           => '2. How we use your data',
        'privacy_use_1'           => 'Process and manage your vehicle rental reservations.',
        'privacy_use_2'           => 'Verify your identity and eligibility to drive.',
        'privacy_use_3'           => 'Send you notifications about reservation status, payments and reminders.',
        'privacy_use_4'           => 'Comply with accounting, tax and legal obligations in the Dominican Republic.',
        'privacy_h_share'         => '3. Sharing information',
        'privacy_share_text'      => 'We do not sell your data. We only share it with providers strictly necessary to operate the service (payment processor, hosting) and when required by law.',
        'privacy_h_storage'       => '4. Storage and security',
        'privacy_storage_text'    => 'Your data is stored on Hostinger servers with encrypted connections (HTTPS). We apply access controls and periodic backups.',
        'privacy_h_rights'        => '5. Your rights',
        'privacy_rights_text'     => 'You can request access, correction or deletion of your data by emailing [email]. We will respond within 30 days.',
        'privacy_h_push'          => '6. Push notifications',
        'privacy_push_text'       => 'Notifications are sent using the official Apple (APNs) and Google (Firebase Cloud Messaging) services. You can turn them off in your device settings at any time.',
        'privacy_h_children'      => '7. Children',
        'privacy_children_text'   => 'The service is intended for people over 21, the minimum legal age to rent vehicles in the Dominican Republic. We do not knowingly collect data from minors.',
        'privacy_h_changes'       => '8. Changes',
        'privacy_changes_text'    => 'Any relevant change will be announced on this page and, when applicable, via in-app notification.',
        'privacy_h_contact'       => '9. Contact',
        'privacy_email'           => 'Email',
        'book_a_car'      => 'Book A Car',
        'learn_more'      => 'Learn More',
        'date_from'       => 'From date:',
        'date_to'         => 'To date:',
        'search'          => 'Search:',
        'premium_fleet'   => 'Premium Fleet',
        'explore_cars'    => 'Explore Our Luxury Cars',
        'explore_sub'     => 'Choose from our exclusive collection of premium vehicles designed for comfort, luxury and performance.',
        'available_now'   => 'Available Now',
        'car_desc_default'=> 'Premium vehicle with comfort, performance and an exclusive driving experience.',
        'per_day'         => '/ Day',
        'rent_now'        => 'Rent Now',
        'no_cars'         => 'No vehicles available for this date range.',
        'invalid_dates'   => 'Invalid dates.',
        'date_order_err'  => 'The start date cannot be greater than the end date.',
        'from_label'      => 'From',
        'to_label'        => 'To',
        'check_avail'     => 'Check Availability',
        'book_now'        => 'Book Now',
        'no_cars_avail'   => 'No cars available',
        'no_cars_msg'     => 'There are no rental vehicles available at this moment.',
        'seats'           => 'Seats',
        'js_select_dates' => 'Please select both dates.',
        'js_date_order'   => 'The end date cannot be before the start date.',
        'js_min_days'     => 'Minimum rental is 3 days.',
        'js_available'    => '✓ Available for those dates.',
        'js_unavailable'  => '✗ Not available for those dates.',
        'hero_mini'       => 'Premium Car Rental Service',
        'hero_title_1'    => 'Experience',
        'hero_title_2'    => 'Premium',
        'hero_title_3'    => 'Car Rentals',
        'hero_title_4'    => 'Built For Every Journey',
        'hero_sub'        => 'Luxury vehicles, premium service and unforgettable driving experiences. Book your ideal car with the most trusted rent car company.',
        'about_mini'      => 'About Us',
        'about_who'       => 'Who We Are',
        'about_title'     => 'Your Trusted Rent Car Company',
        'about_p1'        => 'At [nombre], we believe renting a vehicle should be simple, elegant and reliable. Our goal is to offer every client a premium experience from the moment they choose a vehicle until the end of their trip.',
        'about_p2'        => 'Whether you need a car for business, vacation, personal use or a special occasion, we provide comfortable, clean and well-maintained vehicles ready for the road.',
        'about_p3'        => 'We focus on quality service, transparency and customer satisfaction, making every rental experience smooth and professional.',
        'fleet_title'     => 'Premium Fleet',
        'fleet_desc'      => 'Modern vehicles selected to offer comfort, performance and style for every type of trip.',
        'safe_title'      => 'Safe & Reliable',
        'safe_desc'       => 'Vehicles inspected and maintained to provide confidence, safety and peace of mind.',
        'support_title'   => 'Professional Support',
        'support_desc'    => 'Friendly assistance and clear communication before, during and after your rental.',
        'quality'         => 'Service Quality',
        'support'         => 'Support',
        'clean'           => 'Clean',
        'vehicles'        => 'Vehicles',
        'fast'            => 'Fast',
        'booking_stat'    => 'Booking',
        'cta_title'       => 'Ready To Book Your Car?',
        'cta_sub'         => 'Choose your vehicle today and enjoy a premium rental experience with comfort, confidence and professional service.',
        'view_cars'       => 'View Our Cars',
        'services_mini'   => 'Our Services',
        'services_title'  => 'Premium Rental Services',
        'services_sub'    => 'We offer flexible car rental solutions for daily trips, business travel, airport pickup and special occasions.',
        'what_we_offer'   => 'What We Offer',
        'services_built'  => 'Services Built For Comfort',
        'daily_title'     => 'Daily Car Rental',
        'daily_desc'      => 'Rent a vehicle for one day or multiple days with a fast, simple and professional process.',
        'weekly_title'    => 'Weekly Rental',
        'weekly_desc'     => 'Flexible weekly rental plans designed for clients who need comfort and better pricing.',
        'longterm_title'  => 'Long Term Rental',
        'longterm_desc'   => 'Monthly or long-term rental options for businesses, personal use and extended stays.',
        'airport_title'   => 'Airport Pickup',
        'airport_desc'    => 'Vehicle delivery and pickup service for travelers who need a smooth arrival experience.',
        'safeveh_title'   => 'Safe Vehicles',
        'safeveh_desc'    => 'Clean, inspected and well-maintained vehicles prepared to give you safety and confidence.',
        'support2_title'  => 'Customer Support',
        'support2_desc'   => 'Professional support before, during and after your rental to make your experience better.',
        'how_it_works'    => 'How It Works',
        'easy_process'    => 'Easy Rental Process',
        'step1_title'     => 'Choose Car',
        'step1_desc'      => 'Select the vehicle that fits your trip, style and budget.',
        'step2_title'     => 'Send Request',
        'step2_desc'      => 'Contact us or make your reservation request quickly online.',
        'step3_title'     => 'Confirm Details',
        'step3_desc'      => 'We confirm dates, pickup location, documents and rental terms.',
        'step4_title'     => 'Enjoy Driving',
        'step4_desc'      => 'Receive your vehicle and enjoy a safe, comfortable rental experience.',
        'need_vehicle'    => 'Need A Vehicle Today?',
        'need_sub'        => 'Explore our available fleet and book the right car for your next trip with premium service and full confidence.',
        'view_cars2'      => 'View Cars',
        'contact_title'   => 'Contact Us',
        'contact_sub'     => 'We are here to help you with your next rental experience.',
        'send_message'    => 'Send Us A Message',
        'send_sub'        => 'Fill out the form below and our team will contact you shortly.',
        'full_name'       => 'Full Name',
        'email_addr'      => 'Email Address',
        'phone_num'       => 'Phone Number',
        'subject'         => 'Subject',
        'message'         => 'Message',
        'send_btn'        => 'Send Message',
        'contact_info'    => 'Contact Information',
        'address'         => 'Address',
        'email'           => 'Email',
        'phone'           => 'Phone',
        'hours'           => 'Business Hours',
        'hours_val'       => 'Monday - Saturday',
        'all_rights'      => 'All rights reserved.',
        'footer_company'  => 'Company',
        'footer_services' => 'Services',
        'footer_contact'  => 'Contact',
        'footer_home'     => 'Home',
        'footer_cars'     => 'Cars',
        'footer_about'    => 'About Us',
        'footer_luxury'   => 'Luxury Rental',
        'footer_airport'  => 'Airport Pickup',
        'footer_daily'    => 'Daily Rental',
        'footer_weekly'   => 'Weekly Rental',
        'footer_longterm' => 'Long Term Rental',
        'footer_text'     => 'Premium car rental service designed for comfort, luxury and confidence. We provide modern vehicles, professional support and a smooth rental experience for every journey.',
        'cars_hero_mini'  => 'Our Fleet',
        'cars_hero_title' => 'Choose Your Perfect Car',
        'cars_hero_sub'   => 'Explore our premium fleet of vehicles available for rental.',
        'our_fleet'       => 'Our Fleet',
        'choose_car'      => 'Choose Your Perfect Car',
        'premium_fleet2'  => 'Premium Rental Fleet',
        'available'       => 'Available',
        'per_day2'        => 'Per Day',
        'rent_a_car'      => 'RENT A CAR',
        'notif_title'              => 'Notifications',
        'notif_preferences'        => 'Preferences',
        'notif_mark_all'           => 'Mark all as read',
        'notif_mark_read'          => 'Mark as read',
        'notif_empty'              => 'You have no notifications yet.',
        'notif_new'                => 'NEW',
        'notif_event'              => 'Event',
        'notif_inapp'              => 'In-app',
        'notif_email'              => 'Email',
        'notif_save'               => 'Save',
        'notif_back'               => 'Back',
        'notif_ev_booking_web'     => 'Booking created (web)',
        'notif_ev_booking_created' => 'Booking created (office)',
        'notif_ev_booking_delivered'=> 'Vehicle delivered',
        'notif_ev_booking_canceled'=> 'Booking canceled',
        'notif_ev_payment_received'=> 'Payment received',
        'notif_ev_reminder_return' => 'Return reminder',
        'notif_ev_reminder_pickup' => 'Pickup reminder',
    ],
];

// Expose the dictionary as a real global so callers in any scope (including
// pages routed through Lb::start(), which includes layouts from inside a
// method scope) can read it without re-loading this file.
$GLOBALS['translations'] = $translations;

if (!function_exists('__')) {
    /**
     * Translate a key into the active language.
     *
     * IMPORTANT: this function is intentionally scope-independent. The site
     * is routed through Lb::start(), so lang.php is included from inside a
     * method — which means $translations declared at the top of lang.php is
     * a *local* of that method, not a global. Earlier versions of __() used
     * `global $translations;` and therefore returned the raw key
     * ("nav_home", "hero_title_1", "book_a_car", ...) on every public page.
     *
     * To make this resilient, we keep our own static dictionary inside the
     * function and lazily re-load lang.php at file scope (so $translations
     * lands in $GLOBALS) the very first time __() is called in a request
     * where the globals are missing.
     */
    function __($key) {
        static $dict = null;
        static $lang = null;

        if ($dict === null) {
            if (isset($GLOBALS['translations']) && is_array($GLOBALS['translations'])) {
                $dict = $GLOBALS['translations'];
            } else {
                // Last-resort lazy load — re-execute lang.php so it can
                // populate $GLOBALS['translations'] from a top-level scope.
                include __FILE__;
                $dict = isset($GLOBALS['translations']) && is_array($GLOBALS['translations'])
                    ? $GLOBALS['translations']
                    : [];
            }
            $lang = isset($GLOBALS['LANG']) && is_string($GLOBALS['LANG'])
                ? $GLOBALS['LANG']
                : (isset($_SESSION['lang']) ? strtolower((string)$_SESSION['lang']) : 'es');
            if (!isset($dict[$lang])) {
                $lang = 'es';
            }
        }

        if (isset($dict[$lang][$key])) {
            return $dict[$lang][$key];
        }
        // Fall back to Spanish so the user sees a real label instead of the raw key.
        if (isset($dict['es'][$key])) {
            return $dict['es'][$key];
        }
        return $key;
    }
}
?>

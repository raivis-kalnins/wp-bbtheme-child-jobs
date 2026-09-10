<?php
/**
 * Jobs v3.8.10.85 upgrade layer.
 *
 * Adds optional LinkedIn OpenID Connect sign-in, richer candidate/CV profiles,
 * a high-volume hiring landing-page demo with A/B variants, an employer
 * application pipeline board and optional CRM webhook delivery.
 */
defined( 'ABSPATH' ) || exit;

/**
 * Small v85 dictionary merged into the existing Jobs translation dictionary.
 * The existing demo-translations.json continues to provide the large base set.
 */
function wpbb_jobs_v85_translations() {
    return array(
        'de' => array(
            'High-volume hiring' => 'High-Volume-Recruiting',
            'High-volume Hiring Demo' => 'High-Volume-Recruiting-Demo',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Eine fokussierte Landingpage für wiederkehrende Rollen, schnelle Qualifizierung und eine klarere Bewerber-Pipeline.',
            'Hire repeated roles without repeating the admin.' => 'Besetzen Sie wiederkehrende Rollen, ohne die Verwaltung zu wiederholen.',
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Starten Sie mit einer kurzen Qualifizierung, leiten Sie Personen zu den passenden Stellen und behalten Sie jede Bewerbung in einer Pipeline im Blick.',
            'Find the right role' => 'Die passende Stelle finden',
            'Start matching' => 'Matching starten',
            'Built for repeat hiring' => 'Für wiederkehrende Einstellungen gebaut',
            'Qualify before the full application' => 'Vor der vollständigen Bewerbung qualifizieren',
            'One pipeline for every applicant' => 'Eine Pipeline für alle Bewerber',
            'Test landing-page variants' => 'Landingpage-Varianten testen',
            'Application pipeline' => 'Bewerber-Pipeline',
            'LinkedIn sign-in' => 'LinkedIn-Anmeldung',
            'Continue with LinkedIn' => 'Mit LinkedIn fortfahren',
            'Profile photo' => 'Profilfoto',
            'Current role / employer' => 'Aktuelle Rolle / Arbeitgeber',
            'Portfolio or website' => 'Portfolio oder Website',
            'LinkedIn profile URL' => 'LinkedIn-Profil-URL',
            'Work experience' => 'Berufserfahrung',
            'Education' => 'Ausbildung',
            'Languages' => 'Sprachen',
        ),
        'es' => array(
            'High-volume hiring' => 'Contratación de gran volumen',
            'High-volume Hiring Demo' => 'Demo de contratación de gran volumen',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Una landing enfocada en puestos repetitivos, preselección rápida y un embudo de candidatos más claro.',
            'Hire repeated roles without repeating the admin.' => 'Contrata para puestos repetitivos sin repetir la administración.',
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Empieza con una breve preselección, dirige a cada persona a las vacantes adecuadas y mantén todas las candidaturas visibles en un único embudo.',
            'Find the right role' => 'Encuentra el puesto adecuado',
            'Start matching' => 'Empezar la búsqueda',
            'Built for repeat hiring' => 'Pensado para contratación repetitiva',
            'Qualify before the full application' => 'Preselecciona antes de la solicitud completa',
            'One pipeline for every applicant' => 'Un único embudo para cada candidato',
            'Test landing-page variants' => 'Prueba variantes de landing pages',
            'Application pipeline' => 'Embudo de candidaturas',
            'LinkedIn sign-in' => 'Inicio de sesión con LinkedIn',
            'Continue with LinkedIn' => 'Continuar con LinkedIn',
            'Profile photo' => 'Foto de perfil',
            'Current role / employer' => 'Puesto / empresa actual',
            'Portfolio or website' => 'Portafolio o sitio web',
            'LinkedIn profile URL' => 'URL del perfil de LinkedIn',
            'Work experience' => 'Experiencia laboral',
            'Education' => 'Formación',
            'Languages' => 'Idiomas',
        ),
        'fr' => array(
            'High-volume hiring' => 'Recrutement à fort volume',
            'High-volume Hiring Demo' => 'Démo de recrutement à fort volume',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Une landing page ciblée pour les postes récurrents, la qualification rapide et un pipeline candidats plus clair.',
            'Hire repeated roles without repeating the admin.' => 'Recrutez pour des postes récurrents sans répéter l’administratif.',
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Commencez par une courte étape de qualification, orientez chacun vers les bonnes offres et gardez chaque candidature visible dans un pipeline unique.',
            'Find the right role' => 'Trouver le bon poste',
            'Start matching' => 'Commencer le matching',
            'Built for repeat hiring' => 'Conçu pour les recrutements récurrents',
            'Qualify before the full application' => 'Qualifier avant la candidature complète',
            'One pipeline for every applicant' => 'Un pipeline pour chaque candidat',
            'Test landing-page variants' => 'Tester des variantes de landing pages',
            'Application pipeline' => 'Pipeline de candidatures',
            'LinkedIn sign-in' => 'Connexion LinkedIn',
            'Continue with LinkedIn' => 'Continuer avec LinkedIn',
            'Profile photo' => 'Photo de profil',
            'Current role / employer' => 'Poste / employeur actuel',
            'Portfolio or website' => 'Portfolio ou site web',
            'LinkedIn profile URL' => 'URL du profil LinkedIn',
            'Work experience' => 'Expérience professionnelle',
            'Education' => 'Formation',
            'Languages' => 'Langues',
        ),
        'pl' => array(
            'High-volume hiring' => 'Rekrutacja masowa',
            'High-volume Hiring Demo' => 'Demo rekrutacji masowej',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Skupiona strona docelowa dla powtarzalnych ról, szybkiej kwalifikacji i czytelniejszego lejka kandydatów.',
            'Hire repeated roles without repeating the admin.' => 'Rekrutuj do powtarzalnych ról bez powtarzania administracji.',
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Zacznij od krótkiej kwalifikacji, kieruj ludzi do właściwych ofert i utrzymuj każdą aplikację w jednym lejku.',
            'Find the right role' => 'Znajdź właściwą rolę',
            'Start matching' => 'Rozpocznij dopasowanie',
            'Built for repeat hiring' => 'Stworzone do rekrutacji powtarzalnej',
            'Qualify before the full application' => 'Kwalifikuj przed pełną aplikacją',
            'One pipeline for every applicant' => 'Jeden lejek dla każdego kandydata',
            'Test landing-page variants' => 'Testuj warianty stron docelowych',
            'Application pipeline' => 'Lejek aplikacji',
            'LinkedIn sign-in' => 'Logowanie przez LinkedIn',
            'Continue with LinkedIn' => 'Kontynuuj z LinkedIn',
            'Profile photo' => 'Zdjęcie profilowe',
            'Current role / employer' => 'Obecna rola / pracodawca',
            'Portfolio or website' => 'Portfolio lub strona WWW',
            'LinkedIn profile URL' => 'Adres profilu LinkedIn',
            'Work experience' => 'Doświadczenie zawodowe',
            'Education' => 'Wykształcenie',
            'Languages' => 'Języki',
        ),
        'ru' => array(
            'High-volume hiring' => 'Массовый найм',
            'High-volume Hiring Demo' => 'Демо массового найма',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Целевая посадочная страница для повторяющихся вакансий, быстрой квалификации и более понятной воронки кандидатов.',
            'Hire repeated roles without repeating the admin.' => 'Закрывайте повторяющиеся вакансии без повторения административной работы.',
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Начните с короткой квалификации, направляйте людей к подходящим вакансиям и держите все заявки в одной воронке.',
            'Find the right role' => 'Найти подходящую вакансию',
            'Start matching' => 'Начать подбор',
            'Built for repeat hiring' => 'Для повторяющегося найма',
            'Qualify before the full application' => 'Квалифицируйте до полной заявки',
            'One pipeline for every applicant' => 'Одна воронка для всех кандидатов',
            'Test landing-page variants' => 'Тестируйте варианты посадочных страниц',
            'Application pipeline' => 'Воронка заявок',
            'LinkedIn sign-in' => 'Вход через LinkedIn',
            'Continue with LinkedIn' => 'Продолжить с LinkedIn',
            'Profile photo' => 'Фото профиля',
            'Current role / employer' => 'Текущая должность / работодатель',
            'Portfolio or website' => 'Портфолио или сайт',
            'LinkedIn profile URL' => 'URL профиля LinkedIn',
            'Work experience' => 'Опыт работы',
            'Education' => 'Образование',
            'Languages' => 'Языки',
        ),
        'lv' => array(
            'High-volume hiring' => 'Liela apjoma atlase',
            'High-volume Hiring Demo' => 'Liela apjoma atlases demonstrācija',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Mērķēta lapa atkārtotām lomām, ātrai kvalifikācijai un skaidrākai kandidātu plūsmai.',
            'Hire repeated roles without repeating the admin.' => 'Pieņemiet darbā atkārtotām lomām, neatkārtojot administrēšanu.',
            'Find the right role' => 'Atrodiet piemērotu lomu',
            'Start matching' => 'Sākt atlasi',
            'Built for repeat hiring' => 'Veidots atkārtotai atlasei',
            'Qualify before the full application' => 'Kvalificējiet pirms pilna pieteikuma',
            'One pipeline for every applicant' => 'Viena plūsma visiem kandidātiem',
            'Test landing-page variants' => 'Testējiet mērķlapu variantus',
            'Application pipeline' => 'Pieteikumu plūsma',
            'LinkedIn sign-in' => 'Pieteikšanās ar LinkedIn',
            'Continue with LinkedIn' => 'Turpināt ar LinkedIn',
            'Profile photo' => 'Profila foto',
            'Current role / employer' => 'Pašreizējā loma / darba devējs',
            'Portfolio or website' => 'Portfolio vai tīmekļa vietne',
            'LinkedIn profile URL' => 'LinkedIn profila URL',
            'Work experience' => 'Darba pieredze',
            'Education' => 'Izglītība',
            'Languages' => 'Valodas',
        ),
        'lt' => array(
            'High-volume hiring' => 'Didelės apimties atranka',
            'High-volume Hiring Demo' => 'Didelės apimties atrankos demonstracija',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Tikslingas puslapis pasikartojančioms rolėms, greitam kvalifikavimui ir aiškesniam kandidatų srautui.',
            'Hire repeated roles without repeating the admin.' => 'Samdykite pasikartojančioms rolėms nekartodami administravimo.',
            'Find the right role' => 'Rasti tinkamą rolę',
            'Start matching' => 'Pradėti atranką',
            'Built for repeat hiring' => 'Sukurta pasikartojančiam samdymui',
            'Qualify before the full application' => 'Kvalifikuokite prieš pilną paraišką',
            'One pipeline for every applicant' => 'Vienas srautas kiekvienam kandidatui',
            'Test landing-page variants' => 'Testuokite nukreipimo puslapių variantus',
            'Application pipeline' => 'Paraiškų srautas',
            'LinkedIn sign-in' => 'Prisijungimas su LinkedIn',
            'Continue with LinkedIn' => 'Tęsti su LinkedIn',
            'Profile photo' => 'Profilio nuotrauka',
            'Current role / employer' => 'Dabartinė rolė / darbdavys',
            'Portfolio or website' => 'Portfolio arba svetainė',
            'LinkedIn profile URL' => 'LinkedIn profilio URL',
            'Work experience' => 'Darbo patirtis',
            'Education' => 'Išsilavinimas',
            'Languages' => 'Kalbos',
        ),
        'et' => array(
            'High-volume hiring' => 'Suure mahuga värbamine',
            'High-volume Hiring Demo' => 'Suure mahuga värbamise demo',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Fookustatud sihtleht korduvatele rollidele, kiireks kvalifitseerimiseks ja selgemaks kandidaatide vooks.',
            'Hire repeated roles without repeating the admin.' => 'Värba korduvatele rollidele ilma haldustööd kordamata.',
            'Find the right role' => 'Leia sobiv roll',
            'Start matching' => 'Alusta sobitamist',
            'Built for repeat hiring' => 'Loodud korduvaks värbamiseks',
            'Qualify before the full application' => 'Kvalifitseeri enne täielikku avaldust',
            'One pipeline for every applicant' => 'Üks voog kõigile kandidaatidele',
            'Test landing-page variants' => 'Testi sihtlehe variante',
            'Application pipeline' => 'Kandideerimiste voog',
            'LinkedIn sign-in' => 'LinkedIni sisselogimine',
            'Continue with LinkedIn' => 'Jätka LinkedIniga',
            'Profile photo' => 'Profiilipilt',
            'Current role / employer' => 'Praegune roll / tööandja',
            'Portfolio or website' => 'Portfoolio või veebisait',
            'LinkedIn profile URL' => 'LinkedIni profiili URL',
            'Work experience' => 'Töökogemus',
            'Education' => 'Haridus',
            'Languages' => 'Keeled',
        ),
        'da' => array(
            'High-volume hiring' => 'Højvolumenrekruttering',
            'High-volume Hiring Demo' => 'Demo af højvolumenrekruttering',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'En fokuseret landingsside til gentagne roller, hurtig kvalificering og en tydeligere kandidatpipeline.',
            'Hire repeated roles without repeating the admin.' => 'Ansæt til gentagne roller uden at gentage administrationen.',
            'Find the right role' => 'Find den rigtige rolle',
            'Start matching' => 'Start matchning',
            'Built for repeat hiring' => 'Bygget til gentagne ansættelser',
            'Qualify before the full application' => 'Kvalificér før den fulde ansøgning',
            'One pipeline for every applicant' => 'Én pipeline for alle ansøgere',
            'Test landing-page variants' => 'Test varianter af landingssider',
            'Application pipeline' => 'Ansøgerpipeline',
            'LinkedIn sign-in' => 'LinkedIn-login',
            'Continue with LinkedIn' => 'Fortsæt med LinkedIn',
            'Profile photo' => 'Profilfoto',
            'Current role / employer' => 'Nuværende rolle / arbejdsgiver',
            'Portfolio or website' => 'Portfolio eller website',
            'LinkedIn profile URL' => 'LinkedIn-profil-URL',
            'Work experience' => 'Erhvervserfaring',
            'Education' => 'Uddannelse',
            'Languages' => 'Sprog',
        ),
        'sv' => array(
            'High-volume hiring' => 'Högvolymrekrytering',
            'High-volume Hiring Demo' => 'Demo för högvolymrekrytering',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'En fokuserad landningssida för återkommande roller, snabb kvalificering och en tydligare kandidatpipeline.',
            'Hire repeated roles without repeating the admin.' => 'Rekrytera till återkommande roller utan att upprepa administrationen.',
            'Find the right role' => 'Hitta rätt roll',
            'Start matching' => 'Starta matchning',
            'Built for repeat hiring' => 'Byggt för återkommande rekrytering',
            'Qualify before the full application' => 'Kvalificera före fullständig ansökan',
            'One pipeline for every applicant' => 'En pipeline för alla sökande',
            'Test landing-page variants' => 'Testa varianter av landningssidor',
            'Application pipeline' => 'Ansökningspipeline',
            'LinkedIn sign-in' => 'LinkedIn-inloggning',
            'Continue with LinkedIn' => 'Fortsätt med LinkedIn',
            'Profile photo' => 'Profilbild',
            'Current role / employer' => 'Nuvarande roll / arbetsgivare',
            'Portfolio or website' => 'Portfolio eller webbplats',
            'LinkedIn profile URL' => 'LinkedIn-profilens URL',
            'Work experience' => 'Arbetslivserfarenhet',
            'Education' => 'Utbildning',
            'Languages' => 'Språk',
        ),
        'nb' => array(
            'High-volume hiring' => 'Høyvolumrekruttering',
            'High-volume Hiring Demo' => 'Demo for høyvolumrekruttering',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'En fokusert landingsside for gjentatte roller, rask kvalifisering og en tydeligere kandidatpipeline.',
            'Hire repeated roles without repeating the admin.' => 'Ansett til gjentatte roller uten å gjenta administrasjonen.',
            'Find the right role' => 'Finn riktig rolle',
            'Start matching' => 'Start matching',
            'Built for repeat hiring' => 'Bygget for gjentatte ansettelser',
            'Qualify before the full application' => 'Kvalifiser før full søknad',
            'One pipeline for every applicant' => 'Én pipeline for alle søkere',
            'Test landing-page variants' => 'Test varianter av landingssider',
            'Application pipeline' => 'Søkerpipeline',
            'LinkedIn sign-in' => 'LinkedIn-innlogging',
            'Continue with LinkedIn' => 'Fortsett med LinkedIn',
            'Profile photo' => 'Profilbilde',
            'Current role / employer' => 'Nåværende rolle / arbeidsgiver',
            'Portfolio or website' => 'Portefølje eller nettsted',
            'LinkedIn profile URL' => 'LinkedIn-profil-URL',
            'Work experience' => 'Arbeidserfaring',
            'Education' => 'Utdanning',
            'Languages' => 'Språk',
        ),
        'fi' => array(
            'High-volume hiring' => 'Suuren volyymin rekrytointi',
            'High-volume Hiring Demo' => 'Suuren volyymin rekrytoinnin demo',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Kohdennettu laskeutumissivu toistuviin rooleihin, nopeaan esikarsintaan ja selkeämpään hakijaputkeen.',
            'Hire repeated roles without repeating the admin.' => 'Rekrytoi toistuviin rooleihin ilman hallinnon toistamista.',
            'Find the right role' => 'Löydä sopiva rooli',
            'Start matching' => 'Aloita sovitus',
            'Built for repeat hiring' => 'Rakennettu toistuvaan rekrytointiin',
            'Qualify before the full application' => 'Esikarsi ennen täydellistä hakemusta',
            'One pipeline for every applicant' => 'Yksi putki kaikille hakijoille',
            'Test landing-page variants' => 'Testaa laskeutumissivujen versioita',
            'Application pipeline' => 'Hakijaputki',
            'LinkedIn sign-in' => 'LinkedIn-kirjautuminen',
            'Continue with LinkedIn' => 'Jatka LinkedInillä',
            'Profile photo' => 'Profiilikuva',
            'Current role / employer' => 'Nykyinen rooli / työnantaja',
            'Portfolio or website' => 'Portfolio tai verkkosivusto',
            'LinkedIn profile URL' => 'LinkedIn-profiilin URL',
            'Work experience' => 'Työkokemus',
            'Education' => 'Koulutus',
            'Languages' => 'Kielet',
        ),
        'is' => array(
            'High-volume hiring' => 'Fjöldaráðningar',
            'High-volume Hiring Demo' => 'Sýnishorn fyrir fjöldaráðningar',
            'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.' => 'Markviss áfangasíða fyrir endurtekin störf, hraða hæfismatssíu og skýrari umsóknarleið.',
            'Hire repeated roles without repeating the admin.' => 'Ráðið í endurtekin störf án þess að endurtaka umsýsluna.',
            'Find the right role' => 'Finndu rétta starfið',
            'Start matching' => 'Hefja samsvörun',
            'Built for repeat hiring' => 'Hannað fyrir endurteknar ráðningar',
            'Qualify before the full application' => 'Meta hæfi áður en full umsókn er send',
            'One pipeline for every applicant' => 'Ein ferill fyrir alla umsækjendur',
            'Test landing-page variants' => 'Prófaðu afbrigði áfangasíðna',
            'Application pipeline' => 'Umsóknarferill',
            'LinkedIn sign-in' => 'LinkedIn innskráning',
            'Continue with LinkedIn' => 'Halda áfram með LinkedIn',
            'Profile photo' => 'Prófílmynd',
            'Current role / employer' => 'Núverandi starf / vinnuveitandi',
            'Portfolio or website' => 'Verkefnasafn eða vefsíða',
            'LinkedIn profile URL' => 'Slóð á LinkedIn prófíl',
            'Work experience' => 'Starfsreynsla',
            'Education' => 'Menntun',
            'Languages' => 'Tungumál',
        ),
    );
}

add_filter( 'wpbb_jobs_translation_dictionary', static function( $dictionary, $lang ) {
    $all = wpbb_jobs_v85_translations();
    $copy = function_exists( 'wpbb_jobs_v85_high_volume_copy_translations' ) ? wpbb_jobs_v85_high_volume_copy_translations() : array();
    if ( isset( $all[ $lang ] ) ) $dictionary = array_merge( $dictionary, $all[ $lang ] );
    if ( isset( $copy[ $lang ] ) ) $dictionary = array_merge( $dictionary, $copy[ $lang ] );
    return $dictionary;
}, 20, 2 );

/** Optional LinkedIn OpenID Connect. */
function wpbb_jobs_linkedin_enabled() {
    return (bool) wpbb_jobs_setting( 'linkedin_enabled', 0 ) && wpbb_jobs_setting( 'linkedin_client_id', '' ) && wpbb_jobs_setting( 'linkedin_client_secret', '' );
}

function wpbb_jobs_linkedin_callback_url() {
    // LinkedIn redirect URLs should be absolute URLs without callback query parameters.
    // A dedicated REST path keeps the configured redirect stable and exact.
    return rest_url( 'wpbb-jobs/v1/linkedin/callback' );
}

function wpbb_jobs_linkedin_start_url( $return = '' ) {
    $args = array( 'action' => 'wpbb_jobs_linkedin_start' );
    if ( $return ) $args['return_to'] = $return;
    return add_query_arg( $args, admin_url( 'admin-post.php' ) );
}

function wpbb_jobs_handle_linkedin_start() {
    if ( ! wpbb_jobs_linkedin_enabled() ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'LinkedIn sign-in is not configured yet.', 'wp-bbtheme-child' ) );
    }
    $state = strtolower( wp_generate_password( 40, false, false ) );
    $return_to = esc_url_raw( wp_unslash( $_GET['return_to'] ?? wpbb_jobs_page_url( 'candidate-dashboard' ) ) );
    set_transient( 'wpbb_jobs_linkedin_state_' . $state, array( 'return_to' => $return_to ), 10 * MINUTE_IN_SECONDS );
    $auth_url = add_query_arg( array(
        'response_type' => 'code',
        'client_id'     => wpbb_jobs_setting( 'linkedin_client_id', '' ),
        'redirect_uri'  => wpbb_jobs_linkedin_callback_url(),
        'state'         => $state,
        'scope'         => 'openid profile email',
    ), 'https://www.linkedin.com/oauth/v2/authorization' );
    wp_redirect( $auth_url ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
    exit;
}
add_action( 'admin_post_nopriv_wpbb_jobs_linkedin_start', 'wpbb_jobs_handle_linkedin_start' );
add_action( 'admin_post_wpbb_jobs_linkedin_start', 'wpbb_jobs_handle_linkedin_start' );

function wpbb_jobs_handle_linkedin_callback() {
    $state = sanitize_key( wp_unslash( $_GET['state'] ?? '' ) );
    $stored = $state ? get_transient( 'wpbb_jobs_linkedin_state_' . $state ) : false;
    if ( ! is_array( $stored ) ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'The LinkedIn sign-in session expired. Please try again.', 'wp-bbtheme-child' ) );
    }
    delete_transient( 'wpbb_jobs_linkedin_state_' . $state );
    if ( ! empty( $_GET['error'] ) || empty( $_GET['code'] ) ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'LinkedIn sign-in was cancelled or could not be completed.', 'wp-bbtheme-child' ) );
    }

    $token_response = wp_remote_post( 'https://www.linkedin.com/oauth/v2/accessToken', array(
        'timeout' => 20,
        'body'    => array(
            'grant_type'    => 'authorization_code',
            'code'          => sanitize_text_field( wp_unslash( $_GET['code'] ) ),
            'redirect_uri'  => wpbb_jobs_linkedin_callback_url(),
            'client_id'     => wpbb_jobs_setting( 'linkedin_client_id', '' ),
            'client_secret' => wpbb_jobs_setting( 'linkedin_client_secret', '' ),
        ),
    ) );
    if ( is_wp_error( $token_response ) || 200 !== (int) wp_remote_retrieve_response_code( $token_response ) ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'LinkedIn did not return a usable access token.', 'wp-bbtheme-child' ) );
    }
    $token_body = json_decode( wp_remote_retrieve_body( $token_response ), true );
    $access_token = is_array( $token_body ) ? sanitize_text_field( $token_body['access_token'] ?? '' ) : '';
    if ( ! $access_token ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'LinkedIn did not return a usable access token.', 'wp-bbtheme-child' ) );
    }

    $profile_response = wp_remote_get( 'https://api.linkedin.com/v2/userinfo', array(
        'timeout' => 20,
        'headers' => array( 'Authorization' => 'Bearer ' . $access_token ),
    ) );
    if ( is_wp_error( $profile_response ) || 200 !== (int) wp_remote_retrieve_response_code( $profile_response ) ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'LinkedIn profile details could not be loaded.', 'wp-bbtheme-child' ) );
    }
    $profile = json_decode( wp_remote_retrieve_body( $profile_response ), true );
    $sub = sanitize_text_field( is_array( $profile ) ? ( $profile['sub'] ?? '' ) : '' );
    $email = sanitize_email( is_array( $profile ) ? ( $profile['email'] ?? '' ) : '' );
    $name = sanitize_text_field( is_array( $profile ) ? ( $profile['name'] ?? '' ) : '' );
    $picture = esc_url_raw( is_array( $profile ) ? ( $profile['picture'] ?? '' ) : '' );
    if ( ! $sub || ! is_email( $email ) ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'LinkedIn did not provide the email address needed to create or match an account.', 'wp-bbtheme-child' ) );
    }

    $users = get_users( array( 'meta_key' => '_wpbb_linkedin_sub', 'meta_value' => $sub, 'number' => 1, 'fields' => 'ids' ) );
    $user_id = $users ? (int) $users[0] : (int) email_exists( $email );
    if ( ! $user_id ) {
        $base = sanitize_user( strstr( $email, '@', true ), true ) ?: 'candidate';
        $username = $base;
        $counter = 2;
        while ( username_exists( $username ) ) { $username = $base . $counter; $counter++; }
        $user_id = wp_insert_user( array(
            'user_login'   => $username,
            'user_email'   => $email,
            'display_name' => $name ?: $email,
            'user_pass'    => wp_generate_password( 32, true, true ),
            'role'         => 'wpbb_job_candidate',
        ) );
        if ( is_wp_error( $user_id ) ) {
            wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', $user_id->get_error_message() );
        }
    }
    $user_id = (int) $user_id;
    update_user_meta( $user_id, '_wpbb_linkedin_sub', $sub );
    if ( $picture ) update_user_meta( $user_id, '_wpbb_linkedin_picture_url', $picture );
    if ( $name ) wp_update_user( array( 'ID' => $user_id, 'display_name' => $name ) );

    $resume = wpbb_jobs_get_user_resume( $user_id );
    if ( $resume instanceof WP_Post && $picture ) update_post_meta( $resume->ID, '_wpbb_resume_linkedin_picture_url', $picture );

    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );
    $target = user_can( $user_id, 'manage_options' ) || wpbb_jobs_is_employer( $user_id ) ? wpbb_jobs_page_url( 'employer-dashboard' ) : ( $stored['return_to'] ?: wpbb_jobs_page_url( 'candidate-dashboard' ) );
    wpbb_jobs_redirect_with_notice( $target, 'success', __( 'Signed in with LinkedIn.', 'wp-bbtheme-child' ) );
}
add_action( 'admin_post_nopriv_wpbb_jobs_linkedin_callback', 'wpbb_jobs_handle_linkedin_callback' );
add_action( 'admin_post_wpbb_jobs_linkedin_callback', 'wpbb_jobs_handle_linkedin_callback' );

add_action( 'rest_api_init', static function() {
    register_rest_route( 'wpbb-jobs/v1', '/linkedin/callback', array(
        'methods'             => WP_REST_Server::READABLE,
        'permission_callback' => '__return_true',
        'callback'            => static function() {
            wpbb_jobs_handle_linkedin_callback();
            return new WP_REST_Response( null, 204 );
        },
    ) );
} );

function wpbb_jobs_linkedin_button_html( $return_to = '' ) {
    if ( ! wpbb_jobs_linkedin_enabled() ) return '';
    return '<div class="wpbb-jobs-social-login"><span>' . esc_html__( 'LinkedIn sign-in', 'wp-bbtheme-child' ) . '</span><a class="wpbb-jobs-linkedin-button" href="' . esc_url( wpbb_jobs_linkedin_start_url( $return_to ) ) . '"><strong>in</strong>' . esc_html__( 'Continue with LinkedIn', 'wp-bbtheme-child' ) . '</a></div>';
}

/** Candidate photo + structured CV helpers. */
function wpbb_jobs_handle_profile_image_upload( $field, $resume_id ) {
    if ( empty( $_FILES[ $field ]['name'] ) || empty( $_FILES[ $field ]['tmp_name'] ) ) return 0;
    if ( ! empty( $_FILES[ $field ]['size'] ) && (int) $_FILES[ $field ]['size'] > 5 * MB_IN_BYTES ) return new WP_Error( 'image_too_large', __( 'Profile images must be 5 MB or smaller.', 'wp-bbtheme-child' ) );
    $allowed = array( 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp' );
    $type = wp_check_filetype( sanitize_file_name( wp_unslash( $_FILES[ $field ]['name'] ) ), $allowed );
    if ( empty( $type['ext'] ) || empty( $type['type'] ) ) return new WP_Error( 'invalid_profile_image', __( 'Please upload a JPG, PNG or WebP profile image.', 'wp-bbtheme-child' ) );
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attachment_id = media_handle_upload( $field, $resume_id, array(), array( 'test_form' => false ) );
    if ( ! is_wp_error( $attachment_id ) ) set_post_thumbnail( $resume_id, $attachment_id );
    return $attachment_id;
}

function wpbb_jobs_resume_avatar_html( $resume, $size = 'thumbnail' ) {
    $resume = get_post( $resume );
    if ( ! $resume instanceof WP_Post ) return '';
    $thumb = get_the_post_thumbnail( $resume->ID, $size, array( 'class' => 'wpbb-jobs-resume-photo', 'loading' => 'lazy' ) );
    if ( $thumb ) return $thumb;
    $remote = esc_url( get_post_meta( $resume->ID, '_wpbb_resume_linkedin_picture_url', true ) );
    if ( ! $remote && $resume->post_author ) $remote = esc_url( get_user_meta( $resume->post_author, '_wpbb_linkedin_picture_url', true ) );
    if ( $remote ) return '<img class="wpbb-jobs-resume-photo" src="' . esc_url( $remote ) . '" alt="" loading="lazy" referrerpolicy="no-referrer" />';
    return '<span class="wpbb-jobs-resume-initial" aria-hidden="true">' . esc_html( strtoupper( substr( $resume->post_title, 0, 1 ) ) ) . '</span>';
}

/** Best-effort local DOCX text import for the editable CV builder. */
function wpbb_jobs_extract_docx_text( $attachment_id ) {
    $path = get_attached_file( $attachment_id );
    if ( ! $path || 'docx' !== strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) || ! class_exists( 'ZipArchive' ) ) return '';
    $zip = new ZipArchive();
    if ( true !== $zip->open( $path ) ) return '';
    $xml = $zip->getFromName( 'word/document.xml' );
    $zip->close();
    if ( ! $xml ) return '';
    $xml = str_replace( array( '</w:p>', '</w:tr>', '<w:tab/>' ), array( "\n", "\n", "\t" ), $xml );
    $text = html_entity_decode( wp_strip_all_tags( $xml ), ENT_QUOTES | ENT_XML1, 'UTF-8' );
    $text = preg_replace( "/[\t ]+\n/", "\n", (string) $text );
    $text = preg_replace( "/\n{3,}/", "\n\n", (string) $text );
    $text = (string) $text;
    $text = function_exists( 'mb_substr' ) ? mb_substr( $text, 0, 12000 ) : substr( $text, 0, 12000 );
    return trim( $text );
}

/** High-volume hiring / Homepage 2 demo. */
function wpbb_jobs_high_volume_variant() {
    $forced = sanitize_key( wp_unslash( $_GET['jobs_variant'] ?? '' ) );
    if ( in_array( $forced, array( 'a', 'b' ), true ) ) return $forced;
    $seed = is_user_logged_in() ? 'u:' . get_current_user_id() : 'v:' . ( sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ) . '|' . sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ) );
    return ( abs( crc32( $seed ) ) % 2 ) ? 'b' : 'a';
}

function wpbb_jobs_high_volume_home() {
    $variant = wpbb_jobs_high_volume_variant();
    $jobs_url = wpbb_jobs_page_url( 'jobs' );
    $locations = wpbb_jobs_get_terms_options( 'wpbb_job_location' );
    $types = wpbb_jobs_get_terms_options( 'wpbb_job_type' );
    ob_start();
    ?>
    <div class="wpbb-jobs-hv wpbb-jobs-hv--<?php echo esc_attr( $variant ); ?>" data-variant="<?php echo esc_attr( strtoupper( $variant ) ); ?>">
        <section class="wpbb-jobs-hv-hero">
            <div class="wpbb-jobs-hv-hero__copy">
                <p class="wp-theme-sector-eyebrow"><?php esc_html_e( 'High-volume hiring', 'wp-bbtheme-child' ); ?></p>
                <h1><?php esc_html_e( 'Hire repeated roles without repeating the admin.', 'wp-bbtheme-child' ); ?></h1>
                <p><?php esc_html_e( 'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.', 'wp-bbtheme-child' ); ?></p>
                <?php if ( 'a' === $variant ) : ?><a class="wpbb-jobs-button wpbb-jobs-hv-hero__button" href="#wpbb-hv-filter"><?php esc_html_e( 'Find the right role', 'wp-bbtheme-child' ); ?> →</a><?php endif; ?>
            </div>
            <div id="wpbb-hv-filter" class="wpbb-jobs-hv-filter">
                <p class="wpbb-jobs-hv-filter__label"><?php esc_html_e( 'Find the right role', 'wp-bbtheme-child' ); ?></p>
                <form data-wpbb-jobs-public-form="1" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="wpbb_jobs_high_volume_qualify" />
                    <input type="hidden" name="variant" value="<?php echo esc_attr( $variant ); ?>" />
                    <?php wp_nonce_field( 'wpbb_jobs_high_volume_qualify', 'wpbb_jobs_nonce' ); ?>
                    <label><?php esc_html_e( 'Role or keyword', 'wp-bbtheme-child' ); ?><input type="search" name="job_keyword" placeholder="<?php esc_attr_e( 'e.g. Warehouse operative', 'wp-bbtheme-child' ); ?>" /></label>
                    <div class="wpbb-jobs-hv-filter__grid">
                        <label><?php esc_html_e( 'Location', 'wp-bbtheme-child' ); ?><select name="job_location"><option value=""><?php esc_html_e( 'Any location', 'wp-bbtheme-child' ); ?></option><?php foreach ( $locations as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?></select></label>
                        <label><?php esc_html_e( 'Job type', 'wp-bbtheme-child' ); ?><select name="job_type"><option value=""><?php esc_html_e( 'Any type', 'wp-bbtheme-child' ); ?></option><?php foreach ( $types as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?></select></label>
                    </div>
                    <?php if ( function_exists( 'wpbb_jobs_v86_public_hcaptcha_html' ) ) echo wpbb_jobs_v86_public_hcaptcha_html( 'high-volume-qualifier' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <button class="wpbb-jobs-button" type="submit"><?php esc_html_e( 'Start matching', 'wp-bbtheme-child' ); ?> →</button>
                </form>
            </div>
        </section>
        <section class="wpbb-jobs-hv-role-types">
            <div class="wpbb-jobs-hv-section-head"><p class="wp-theme-sector-eyebrow"><?php esc_html_e( 'Repeat-role recruitment', 'wp-bbtheme-child' ); ?></p><h2><?php esc_html_e( 'Built for roles you hire again and again.', 'wp-bbtheme-child' ); ?></h2><p><?php esc_html_e( 'Create one clear recruitment route, then reuse it for every intake, location or shift.', 'wp-bbtheme-child' ); ?></p></div>
            <div class="wpbb-jobs-hv-role-grid">
                <?php foreach ( array( 'Firefighter', 'Warehouse Operative', 'Care Assistant', 'Delivery Driver', 'Customer Service Advisor', 'Retail Assistant' ) as $role_label ) : ?>
                    <a href="<?php echo esc_url( add_query_arg( 'job_keyword', $role_label, $jobs_url ) ); ?>"><span aria-hidden="true">→</span><strong><?php echo esc_html( $role_label ); ?></strong><small><?php esc_html_e( 'Reusable hiring journey', 'wp-bbtheme-child' ); ?></small></a>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="wpbb-jobs-hv-benefits">
            <article><span>01</span><h3><?php esc_html_e( 'Built for repeat hiring', 'wp-bbtheme-child' ); ?></h3><p><?php esc_html_e( 'Use one clear route for roles that reopen every week or every season.', 'wp-bbtheme-child' ); ?></p></article>
            <article><span>02</span><h3><?php esc_html_e( 'Qualify before the full application', 'wp-bbtheme-child' ); ?></h3><p><?php esc_html_e( 'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.', 'wp-bbtheme-child' ); ?></p></article>
            <article><span>03</span><h3><?php esc_html_e( 'One pipeline for every applicant', 'wp-bbtheme-child' ); ?></h3><p><?php esc_html_e( 'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.', 'wp-bbtheme-child' ); ?></p></article>
            <article><span>04</span><h3><?php esc_html_e( 'Test landing-page variants', 'wp-bbtheme-child' ); ?></h3><p><?php esc_html_e( 'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.', 'wp-bbtheme-child' ); ?></p></article>
        </section>
        <section class="wpbb-jobs-hv-pipeline-preview">
            <div><p class="wp-theme-sector-eyebrow"><?php esc_html_e( 'Application pipeline', 'wp-bbtheme-child' ); ?></p><h2><?php esc_html_e( 'A simple ATS-style flow is already built in.', 'wp-bbtheme-child' ); ?></h2><p><?php esc_html_e( 'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.', 'wp-bbtheme-child' ); ?></p></div>
            <div class="wpbb-jobs-hv-stages"><span><?php esc_html_e( 'New', 'wp-bbtheme-child' ); ?></span><span><?php esc_html_e( 'Reviewing', 'wp-bbtheme-child' ); ?></span><span><?php esc_html_e( 'Shortlisted', 'wp-bbtheme-child' ); ?></span><span><?php esc_html_e( 'Interview', 'wp-bbtheme-child' ); ?></span><span><?php esc_html_e( 'Offer made', 'wp-bbtheme-child' ); ?></span><span><?php esc_html_e( 'Hired', 'wp-bbtheme-child' ); ?></span></div>
        </section>
        <section class="wpbb-jobs-hv-cta"><div><h2><?php esc_html_e( 'Ready to see the live job board?', 'wp-bbtheme-child' ); ?></h2><p><?php esc_html_e( 'Use the full filters, vacancy pages and candidate application flow.', 'wp-bbtheme-child' ); ?></p></div><a class="wpbb-jobs-button" href="<?php echo esc_url( $jobs_url ); ?>"><?php esc_html_e( 'Search jobs', 'wp-bbtheme-child' ); ?> →</a></section>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'wpbb_jobs_high_volume_home', 'wpbb_jobs_high_volume_home' );

function wpbb_jobs_handle_high_volume_qualify() {
    check_admin_referer( 'wpbb_jobs_high_volume_qualify', 'wpbb_jobs_nonce' );
    if ( function_exists( 'wpbb_jobs_v86_verify_public_hcaptcha' ) ) {
        $captcha = wpbb_jobs_v86_verify_public_hcaptcha();
        if ( is_wp_error( $captcha ) ) {
            wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'home-2' ), 'error', $captcha->get_error_message() );
        }
    }
    $args = array();
    $keyword = sanitize_text_field( wp_unslash( $_POST['job_keyword'] ?? '' ) );
    $location = absint( $_POST['job_location'] ?? 0 );
    $type = absint( $_POST['job_type'] ?? 0 );
    if ( $keyword ) $args['job_keyword'] = $keyword;
    if ( $location ) $args['job_location'] = $location;
    if ( $type ) $args['job_type'] = $type;
    $variant = sanitize_key( wp_unslash( $_POST['variant'] ?? '' ) );
    if ( in_array( $variant, array( 'a', 'b' ), true ) ) $args['hv_variant'] = $variant;
    wp_safe_redirect( add_query_arg( $args, wpbb_jobs_page_url( 'jobs' ) ) );
    exit;
}
add_action( 'admin_post_wpbb_jobs_high_volume_qualify', 'wpbb_jobs_handle_high_volume_qualify' );
add_action( 'admin_post_nopriv_wpbb_jobs_high_volume_qualify', 'wpbb_jobs_handle_high_volume_qualify' );

/** Employer pipeline board. */
function wpbb_jobs_application_pipeline_html( $applications, $jobs = array() ) {
    $applications = is_array( $applications ) ? $applications : array();
    if ( ! $applications ) return '';
    $statuses = wpbb_jobs_application_statuses();
    $active_stages = array( 'new', 'reviewing', 'shortlisted', 'interview', 'offered', 'hired' );
    $selected_job = absint( $_GET['pipeline_job'] ?? 0 );
    if ( $selected_job ) {
        $applications = array_values( array_filter( $applications, static function( $app ) use ( $selected_job ) { return $selected_job === absint( get_post_meta( $app->ID, '_wpbb_application_job_id', true ) ); } ) );
    }
    ob_start();
    ?>
    <section class="wpbb-jobs-panel wpbb-jobs-pipeline-panel">
        <div class="wpbb-jobs-pipeline-head"><div><p class="wp-theme-sector-eyebrow"><?php esc_html_e( 'High-volume hiring', 'wp-bbtheme-child' ); ?></p><h2><?php esc_html_e( 'Application pipeline', 'wp-bbtheme-child' ); ?></h2></div>
            <?php if ( $jobs ) : ?><form method="get"><label class="screen-reader-text" for="pipeline-job"><?php esc_html_e( 'Filter pipeline by job', 'wp-bbtheme-child' ); ?></label><select id="pipeline-job" name="pipeline_job" onchange="this.form.submit()"><option value=""><?php esc_html_e( 'All jobs', 'wp-bbtheme-child' ); ?></option><?php foreach ( $jobs as $job ) : ?><option value="<?php echo esc_attr( $job->ID ); ?>" <?php selected( $selected_job, $job->ID ); ?>><?php echo esc_html( $job->post_title ); ?></option><?php endforeach; ?></select></form><?php endif; ?>
        </div>
        <div class="wpbb-jobs-pipeline">
            <?php foreach ( $active_stages as $stage ) : $stage_apps = array_values( array_filter( $applications, static function( $app ) use ( $stage ) { return $stage === ( get_post_meta( $app->ID, '_wpbb_application_status', true ) ?: 'new' ); } ) ); ?>
                <div class="wpbb-jobs-pipeline__stage is-<?php echo esc_attr( $stage ); ?>"><div class="wpbb-jobs-pipeline__stage-head"><strong><?php echo esc_html( $statuses[ $stage ] ?? ucfirst( $stage ) ); ?></strong><span><?php echo esc_html( count( $stage_apps ) ); ?></span></div>
                    <div class="wpbb-jobs-pipeline__cards"><?php if ( ! $stage_apps ) : ?><p class="wpbb-jobs-pipeline__empty">—</p><?php else : foreach ( $stage_apps as $app ) : $job_id = absint( get_post_meta( $app->ID, '_wpbb_application_job_id', true ) ); ?>
                        <article><strong><?php echo esc_html( get_post_meta( $app->ID, '_wpbb_application_name', true ) ); ?></strong><span><?php echo esc_html( get_the_title( $job_id ) ); ?></span><small><?php echo esc_html( get_the_date( '', $app ) ); ?></small>
                            <?php if ( 'hired' !== $stage ) : ?><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="wpbb_jobs_application_status" /><input type="hidden" name="application_id" value="<?php echo esc_attr( $app->ID ); ?>" /><?php wp_nonce_field( 'wpbb_jobs_application_status_' . $app->ID, 'wpbb_jobs_nonce' ); ?><select name="application_status" aria-label="<?php esc_attr_e( 'Update application status', 'wp-bbtheme-child' ); ?>"><?php foreach ( $statuses as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $stage, $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select><button class="wpbb-jobs-link-button" type="submit"><?php esc_html_e( 'Save', 'wp-bbtheme-child' ); ?></button></form><?php endif; ?>
                        </article>
                    <?php endforeach; endif; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/** Optional signed CRM webhook. */
function wpbb_jobs_crm_payload( $application_id, $event ) {
    $app = get_post( $application_id );
    if ( ! $app instanceof WP_Post || 'wpbb_application' !== $app->post_type ) return array();
    $job_id = absint( get_post_meta( $application_id, '_wpbb_application_job_id', true ) );
    $company_id = absint( get_post_meta( $application_id, '_wpbb_application_company_id', true ) );
    return array(
        'event'       => sanitize_key( $event ),
        'site'        => home_url( '/' ),
        'application' => array(
            'id'      => $application_id,
            'name'    => get_post_meta( $application_id, '_wpbb_application_name', true ),
            'email'   => get_post_meta( $application_id, '_wpbb_application_email', true ),
            'phone'   => get_post_meta( $application_id, '_wpbb_application_phone', true ),
            'message' => get_post_meta( $application_id, '_wpbb_application_message', true ),
            'status'  => get_post_meta( $application_id, '_wpbb_application_status', true ) ?: 'new',
            'created' => get_post_time( DATE_ATOM, true, $application_id ),
        ),
        'job' => array( 'id' => $job_id, 'title' => get_the_title( $job_id ), 'url' => $job_id ? get_permalink( $job_id ) : '' ),
        'company' => array( 'id' => $company_id, 'name' => $company_id ? get_the_title( $company_id ) : '' ),
    );
}

function wpbb_jobs_send_crm_webhook( $application_id, $event = 'application.updated' ) {
    $url = esc_url_raw( wpbb_jobs_setting( 'crm_webhook_url', '' ) );
    if ( ! $url ) return;
    $payload = wpbb_jobs_crm_payload( $application_id, $event );
    if ( ! $payload ) return;
    $body = wp_json_encode( $payload );
    $headers = array( 'Content-Type' => 'application/json', 'X-WPBB-Jobs-Event' => sanitize_key( $event ) );
    $secret = (string) wpbb_jobs_setting( 'crm_webhook_secret', '' );
    if ( $secret ) $headers['X-WPBB-Jobs-Signature'] = 'sha256=' . hash_hmac( 'sha256', $body, $secret );
    wp_remote_post( $url, array( 'timeout' => 5, 'blocking' => false, 'headers' => $headers, 'body' => $body, 'data_format' => 'body' ) );
}
add_action( 'wpbb_jobs_application_created', static function( $application_id ) { wpbb_jobs_send_crm_webhook( $application_id, 'application.created' ); } );
add_action( 'wpbb_jobs_application_status_changed', static function( $application_id ) { wpbb_jobs_send_crm_webhook( $application_id, 'application.status_changed' ); } );

/** Additional translated copy used by the high-volume landing-page demo. */
function wpbb_jobs_v85_high_volume_copy_translations() {
    return array(
        'de' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Starten Sie mit einer kurzen Qualifizierung, leiten Sie Personen zu den passenden Stellen und behalten Sie jede Bewerbung in einer Pipeline im Blick.',
            'Use one clear route for roles that reopen every week or every season.' => 'Nutzen Sie einen klaren Weg für Rollen, die jede Woche oder jede Saison erneut geöffnet werden.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Fragen Sie zuerst nach Rolle, Standort und Arbeitsmodell und leiten Sie die Personen dann zu den relevantesten Stellen.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Führen Sie Bewerbungen im Arbeitgeber-Dashboard durch Prüfung, Shortlist, Interview, Angebot und Einstellung.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'Variante A beginnt mit der Qualifizierungsbotschaft, Variante B mit dem Filterformular. Für QA können Sie ?jobs_variant=a oder ?jobs_variant=b ergänzen.',
            'A simple ATS-style flow is already built in.' => 'Ein einfacher ATS-ähnlicher Ablauf ist bereits integriert.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Behalten Sie den Prozess in WordPress oder senden Sie Bewerbungsereignisse über den optional signierten Webhook an ein CRM.',
            'Ready to see the live job board?' => 'Bereit für das Live-Jobboard?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Nutzen Sie die vollständigen Filter, Stellenanzeigen und den Bewerbungsprozess.',
        ),
        'es' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Empieza con una breve preselección, dirige a cada persona a las vacantes adecuadas y mantén todas las candidaturas visibles en un único embudo.',
            'Use one clear route for roles that reopen every week or every season.' => 'Usa un recorrido claro para puestos que se vuelven a abrir cada semana o cada temporada.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Pregunta primero por el puesto, la ubicación y la modalidad de trabajo y después dirige a cada persona a las vacantes más relevantes.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Mueve las candidaturas por revisión, preselección, entrevista, oferta y contratación desde el panel del empleador.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'La variante A empieza con el mensaje de preselección y la variante B con el formulario de filtros. Añade ?jobs_variant=a o ?jobs_variant=b para QA.',
            'A simple ATS-style flow is already built in.' => 'Ya incluye un flujo sencillo de tipo ATS.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Mantén el proceso dentro de WordPress o envía los eventos de candidaturas a un CRM mediante el webhook firmado opcional.',
            'Ready to see the live job board?' => '¿Listo para ver el portal de empleo en vivo?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Usa todos los filtros, las páginas de vacantes y el flujo de solicitud de candidatos.',
        ),
        'fr' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Commencez par une courte étape de qualification, orientez chacun vers les bonnes offres et gardez chaque candidature visible dans un pipeline unique.',
            'Use one clear route for roles that reopen every week or every season.' => 'Utilisez un parcours clair pour les postes qui rouvrent chaque semaine ou chaque saison.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Demandez d’abord le poste, le lieu et le mode de travail, puis orientez les candidats vers les offres les plus pertinentes.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Faites avancer les candidatures de la revue à la présélection, l’entretien, l’offre et l’embauche depuis le tableau de bord employeur.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'La variante A commence par le message de qualification, la variante B par le formulaire de filtre. Ajoutez ?jobs_variant=a ou ?jobs_variant=b pour les tests QA.',
            'A simple ATS-style flow is already built in.' => 'Un flux simple de type ATS est déjà intégré.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Gardez le processus dans WordPress ou transmettez les événements de candidature à un CRM grâce au webhook signé optionnel.',
            'Ready to see the live job board?' => 'Prêt à voir le job board en direct ?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Utilisez tous les filtres, les pages d’offres et le parcours de candidature.',
        ),
        'pl' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Zacznij od krótkiej kwalifikacji, kieruj ludzi do właściwych ofert i utrzymuj każdą aplikację w jednym lejku.',
            'Use one clear route for roles that reopen every week or every season.' => 'Użyj jednej jasnej ścieżki dla ról otwieranych ponownie co tydzień lub sezonowo.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Najpierw zapytaj o rolę, lokalizację i model pracy, a następnie kieruj kandydatów do najbardziej odpowiednich ofert.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Przesuwaj aplikacje przez weryfikację, krótką listę, rozmowę, ofertę i zatrudnienie z panelu pracodawcy.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'Wariant A zaczyna od komunikatu kwalifikacyjnego, a wariant B od formularza filtrów. Do testów QA dodaj ?jobs_variant=a lub ?jobs_variant=b.',
            'A simple ATS-style flow is already built in.' => 'Prosty przepływ w stylu ATS jest już wbudowany.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Prowadź proces w WordPressie albo wysyłaj zdarzenia aplikacji do CRM przez opcjonalny podpisany webhook.',
            'Ready to see the live job board?' => 'Gotowy, aby zobaczyć działającą tablicę ofert?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Użyj pełnych filtrów, stron ofert i procesu aplikowania kandydatów.',
        ),
        'ru' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Начните с короткой квалификации, направляйте людей к подходящим вакансиям и держите все заявки в одной воронке.',
            'Use one clear route for roles that reopen every week or every season.' => 'Используйте один понятный путь для вакансий, которые открываются каждую неделю или сезон.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Сначала спросите о роли, локации и формате работы, затем направьте кандидатов к наиболее подходящим вакансиям.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Перемещайте заявки через этапы проверки, шорт-листа, интервью, предложения и найма из кабинета работодателя.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'Вариант A начинается с квалификационного сообщения, вариант B — с формы фильтров. Для QA добавьте ?jobs_variant=a или ?jobs_variant=b.',
            'A simple ATS-style flow is already built in.' => 'Простой процесс в стиле ATS уже встроен.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Оставьте процесс внутри WordPress или отправляйте события заявок в CRM через дополнительный подписанный webhook.',
            'Ready to see the live job board?' => 'Готовы посмотреть работающую доску вакансий?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Используйте полные фильтры, страницы вакансий и процесс подачи заявки.',
        ),
        'lv' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Sāciet ar īsu kvalifikācijas soli, novirziet cilvēkus uz atbilstošajām vakancēm un saglabājiet visus pieteikumus redzamus vienā plūsmā.',
            'Use one clear route for roles that reopen every week or every season.' => 'Izmantojiet vienu skaidru ceļu lomām, kas tiek atvērtas katru nedēļu vai sezonu.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Vispirms pajautājiet lomu, atrašanās vietu un darba modeli, pēc tam novirziet cilvēkus uz atbilstošākajām vakancēm.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Darba devēja panelī virziet pieteikumus caur izskatīšanu, īso sarakstu, interviju, piedāvājumu un pieņemšanu darbā.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'A variants sākas ar kvalifikācijas ziņu, B variants — ar filtra formu. QA testiem pievienojiet ?jobs_variant=a vai ?jobs_variant=b.',
            'A simple ATS-style flow is already built in.' => 'Vienkārša ATS tipa plūsma jau ir iebūvēta.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Saglabājiet procesu WordPress vai pārsūtiet pieteikumu notikumus uz CRM ar izvēles parakstītu webhook.',
            'Ready to see the live job board?' => 'Gatavs apskatīt aktīvo darba sludinājumu vietni?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Izmantojiet pilnos filtrus, vakanču lapas un kandidātu pieteikšanās procesu.',
        ),
        'lt' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Pradėkite nuo trumpo kvalifikavimo, nukreipkite žmones į tinkamas pozicijas ir visas paraiškas laikykite matomas viename sraute.',
            'Use one clear route for roles that reopen every week or every season.' => 'Naudokite vieną aiškų kelią rolėms, kurios atidaromos kas savaitę ar sezoną.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Pirmiausia paklauskite apie rolę, vietą ir darbo modelį, tada nukreipkite žmones į aktualiausias pozicijas.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Darbdavio skydelyje perkelkite paraiškas per peržiūrą, trumpąjį sąrašą, pokalbį, pasiūlymą ir įdarbinimą.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'A variantas prasideda kvalifikavimo žinute, B variantas — filtro forma. QA testams pridėkite ?jobs_variant=a arba ?jobs_variant=b.',
            'A simple ATS-style flow is already built in.' => 'Paprastas ATS tipo srautas jau įdiegtas.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Laikykite procesą WordPress sistemoje arba persiųskite paraiškų įvykius į CRM per pasirenkamą pasirašytą webhook.',
            'Ready to see the live job board?' => 'Pasiruošę pamatyti veikiančią darbo skelbimų lentą?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Naudokite visus filtrus, pozicijų puslapius ir kandidatų paraiškų procesą.',
        ),
        'et' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Alusta lühikese kvalifitseerimisega, suuna inimesed õigetele töökohtadele ja hoia kõik avaldused nähtaval ühes voos.',
            'Use one clear route for roles that reopen every week or every season.' => 'Kasuta üht selget teekonda rollidele, mis avanevad igal nädalal või hooajal.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Küsi esmalt rolli, asukohta ja töökorraldust ning suuna inimesed seejärel kõige sobivamatele töökohtadele.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Liiguta avaldusi tööandja juhtpaneelil läbi ülevaatuse, valiku, intervjuu, pakkumise ja palkamise.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'Variant A algab kvalifitseeriva sõnumiga ja variant B filtrivormiga. QA testimiseks lisa ?jobs_variant=a või ?jobs_variant=b.',
            'A simple ATS-style flow is already built in.' => 'Lihtne ATS-tüüpi töövoog on juba sisse ehitatud.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Hoia protsess WordPressis või edasta avalduste sündmused CRM-i valikulise allkirjastatud webhooki kaudu.',
            'Ready to see the live job board?' => 'Kas oled valmis nägema töötavat tööportaali?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Kasuta täielikke filtreid, töökuulutuste lehti ja kandidaatide kandideerimisvoogu.',
        ),
        'da' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Start med et kort kvalificeringstrin, send folk til de rigtige stillinger, og hold alle ansøgninger synlige i én pipeline.',
            'Use one clear route for roles that reopen every week or every season.' => 'Brug én tydelig vej til roller, der genåbnes hver uge eller hver sæson.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Spørg først efter rolle, lokation og arbejdsform, og send derefter folk til de mest relevante stillinger.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Flyt ansøgninger gennem vurdering, shortlist, samtale, tilbud og ansættelse fra arbejdsgiverens dashboard.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'Variant A starter med kvalificeringsbudskabet, variant B med filterformularen. Til QA kan du tilføje ?jobs_variant=a eller ?jobs_variant=b.',
            'A simple ATS-style flow is already built in.' => 'Et enkelt ATS-lignende flow er allerede indbygget.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Behold processen i WordPress, eller send ansøgningshændelser til et CRM via det valgfrie signerede webhook.',
            'Ready to see the live job board?' => 'Klar til at se det aktive jobboard?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Brug alle filtre, stillingssider og kandidatens ansøgningsflow.',
        ),
        'sv' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Börja med ett kort kvalificeringssteg, led personer till rätt jobb och håll alla ansökningar synliga i en pipeline.',
            'Use one clear route for roles that reopen every week or every season.' => 'Använd en tydlig väg för roller som öppnas igen varje vecka eller säsong.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Fråga först om roll, plats och arbetsform och skicka sedan personer till de mest relevanta jobben.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Flytta ansökningar genom granskning, kortlista, intervju, erbjudande och anställning från arbetsgivarens dashboard.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'Variant A börjar med kvalificeringsbudskapet, variant B med filterformuläret. Lägg till ?jobs_variant=a eller ?jobs_variant=b för QA.',
            'A simple ATS-style flow is already built in.' => 'Ett enkelt ATS-liknande flöde är redan inbyggt.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Behåll processen i WordPress eller vidarebefordra ansökningshändelser till ett CRM via den valfria signerade webhooken.',
            'Ready to see the live job board?' => 'Redo att se den aktiva jobbportalen?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Använd alla filter, jobbsidor och kandidatens ansökningsflöde.',
        ),
        'nb' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Start med et kort kvalifiseringstrinn, send folk til de riktige stillingene og hold alle søknader synlige i én pipeline.',
            'Use one clear route for roles that reopen every week or every season.' => 'Bruk én tydelig vei for roller som åpnes igjen hver uke eller sesong.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Spør først om rolle, sted og arbeidsform, og send deretter folk til de mest relevante stillingene.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Flytt søknader gjennom vurdering, kortliste, intervju, tilbud og ansettelse fra arbeidsgiverpanelet.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'Variant A starter med kvalifiseringsbudskapet, variant B med filterskjemaet. Legg til ?jobs_variant=a eller ?jobs_variant=b for QA.',
            'A simple ATS-style flow is already built in.' => 'En enkel ATS-lignende flyt er allerede innebygd.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Behold prosessen i WordPress eller videresend søknadshendelser til et CRM via den valgfrie signerte webhooken.',
            'Ready to see the live job board?' => 'Klar til å se den aktive jobbportalen?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Bruk alle filtre, stillingssider og kandidatens søknadsflyt.',
        ),
        'fi' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Aloita lyhyellä esikarsinnalla, ohjaa ihmiset oikeisiin tehtäviin ja pidä kaikki hakemukset näkyvissä yhdessä putkessa.',
            'Use one clear route for roles that reopen every week or every season.' => 'Käytä yhtä selkeää polkua tehtäville, jotka avataan uudelleen viikoittain tai kausittain.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Kysy ensin tehtävä, sijainti ja työskentelytapa ja ohjaa sitten ihmiset sopivimpiin avoimiin tehtäviin.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Siirrä hakemuksia työnantajan hallintapaneelissa tarkistuksesta lyhyelle listalle, haastatteluun, tarjoukseen ja palkkaukseen.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'Versio A alkaa esikarsintaviestillä ja versio B suodatinlomakkeella. QA-testaukseen lisää ?jobs_variant=a tai ?jobs_variant=b.',
            'A simple ATS-style flow is already built in.' => 'Yksinkertainen ATS-tyylinen työnkulku on jo sisäänrakennettu.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Pidä prosessi WordPressissä tai välitä hakemustapahtumat CRM:ään valinnaisen allekirjoitetun webhookin avulla.',
            'Ready to see the live job board?' => 'Valmis näkemään toimivan työpaikkasivuston?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Käytä kaikkia suodattimia, työpaikkasivuja ja hakijan hakemuspolkua.',
        ),
        'is' => array(
            'Start with a short qualifying step, route people to the right vacancies and keep every application visible in one pipeline.' => 'Byrjaðu á stuttu hæfismati, vísaðu fólki á rétt störf og haltu öllum umsóknum sýnilegum í einum ferli.',
            'Use one clear route for roles that reopen every week or every season.' => 'Notaðu eina skýra leið fyrir störf sem opna aftur vikulega eða eftir árstíðum.',
            'Ask for the role, location and working pattern first, then send people to the most relevant vacancies.' => 'Spyrðu fyrst um starf, staðsetningu og vinnufyrirkomulag og sendu síðan fólk á viðeigandi störf.',
            'Move applications through reviewing, shortlist, interview, offer and hire from the employer dashboard.' => 'Færðu umsóknir í gegnum yfirferð, úrval, viðtal, tilboð og ráðningu í stjórnborði vinnuveitanda.',
            'Variant A leads with the qualifying message; Variant B leads with the filter form. Add ?jobs_variant=a or ?jobs_variant=b for QA.' => 'Afbrigði A byrjar á hæfisskilaboðum en afbrigði B á síunarformi. Bættu við ?jobs_variant=a eða ?jobs_variant=b fyrir QA-prófanir.',
            'A simple ATS-style flow is already built in.' => 'Einfalt ATS-líkt ferli er þegar innbyggt.',
            'Keep the process inside WordPress or forward application events to a CRM with the optional signed webhook.' => 'Haltu ferlinu inni í WordPress eða sendu umsóknaratburði í CRM með valfrjálsum undirrituðum webhook.',
            'Ready to see the live job board?' => 'Tilbúin að sjá virka starfavefinn?',
            'Use the full filters, vacancy pages and candidate application flow.' => 'Notaðu allar síur, starfssíður og umsóknarferli kandidata.',
        ),
    );
}

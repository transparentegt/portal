<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Controlador para páginas con lógica
 */
class Index extends AbstractActionController
{
    public function indexAction()
    {
        $db = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        /* @var $db \Doctrine\ORM\EntityManager */
        $sql  = '   SELECT "domicilios de proveedores" as entidad, COUNT(t.id) as total FROM domicilios t
                UNION
                    SELECT "empleados municipales" as entidad,  COUNT(t.id) as total FROM empleado_municipal t
                UNION
                    SELECT "pagos realizados" as entidad,  COUNT(t.id) as total FROM pago t
                UNION
                    SELECT "proveedores" as entidad,  COUNT(t.id) as total FROM proveedor t
                UNION
                    SELECT "representantes legales" as entidad,  COUNT(t.id) as total FROM rep_legal t
                ';
        $rsm  = new ResultSetMapping($db);
        $rsm->addScalarResult('entidad', 'entidad');
        $rsm->addScalarResult('total',   'total');
        $totals = $db->createNativeQuery($sql, $rsm)->getResult();
        return new ViewModel(compact('totals'));
    }

    /**
     * Página de contacto
     */
    public function contactAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $vars = [
                'secret' => '6Lf_lwcTAAAAAKJEoGlninIccUEYIhtZsvvpn7yH',
                'response' => $request->getPost()->get('g-recaptcha-response'),
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ];
            $postdata = http_build_query($vars);
            $url      = 'https://www.google.com/recaptcha/api/siteverify';
            $opts     = ['http' => [
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                ]
            ];
            $context = stream_context_create($opts);
            $content = file_get_contents($url, false, $context, -1);
            $captcha = json_decode($content)->success;

            $flash = $this->flashmessenger();
            /* @var $flash \Zend\Mvc\Controller\Plugin\FlashMessenger */
            if (!$captcha) {
                $flash->addErrorMessage('El sistema no detectó el bloqueo anti SPAM. Por favor inténtelo de nuevo.');
            } else {
                $data = $request->getPost()->get('contact');
                $mail = new \Zend\Mail\Message();
                $mail->setBody("Contacto:
                    nombre: {$data['name']}
                    email:  {$data['email']}
                    comments: {$data['comments']}
                ");
                $mail->setFrom('info@transparente.gt');
                $mail->addTo('info@transparente.gt');
                $mail->setSubject('[Transparente] contacto');
                (new \Zend\Mail\Transport\Sendmail())->send($mail);
                $flash->addSuccessMessage('Su información ha sido enviada. Pronto nos contactaremos de regreso. Gracias.');
            }
            return $this->redirect()->toUrl($request->getHeader('referer')->getUri());
        }

        return new ViewModel();
    }

}
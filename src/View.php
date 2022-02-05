<?php
namespace Base;

use App\Model\User;

/**
 * Class View
 * @package Base
 *
 */
class View
{
    const RENDER_TYPE_NATIVE = 1;
    const RENDER_TYPE_TWIG = 2;
    private int $_renderType;
    /** @var \Twig\Environment */
    private $_twig;

    private string $templatePath = '';
    private array $data = [];

    public function __construct(int $renderType = self::RENDER_TYPE_NATIVE)
    {
        $this->templatePath = PROJECT_ROOT_DIR . DIRECTORY_SEPARATOR . 'app/View';
        $this->_renderType = $renderType;

    }

    public function setRenderType(int $renderType)
    {
        if (!in_array($renderType, [self::RENDER_TYPE_NATIVE, self::RENDER_TYPE_TWIG])) {
            //throw new \Exception('Wrong render type: ' . $renderType);
        }
        $this->_renderType = $renderType;
    }

    public function getTwig(): \Twig\Environment
    {
        if (!$this->_twig) {
            $path = $this->templatePath . DIRECTORY_SEPARATOR;
            $loader = new \Twig\Loader\FilesystemLoader($path);
            $this->_twig = new \Twig\Environment(
                $loader
//                ['cache' => $path . '_cache', 'autoescape' => false]
            );
        }

        return $this->_twig;
    }

    public function assign(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    public function render(string $tpl, $data = []): string
    {
        switch ($this->_renderType) {
            case self::RENDER_TYPE_NATIVE:
                $this->data += $data;
                foreach ($data as $key => $value) {
                    $this->data[$key] = $value;
                }
                ob_start();
                include $this->templatePath . DIRECTORY_SEPARATOR . $tpl;
                return ob_get_clean();
                break;

            case self::RENDER_TYPE_TWIG:
                $twig = $this->getTwig();
                ob_start(null, PHP_OUTPUT_HANDLER_STDFLAGS);
                try {

                    $this->data += $data;
                    //print_r($data['user']);
                    echo $twig->render($tpl, $this->data);
                } catch (\Exception $e) {
                    trigger_error($e->getMessage());
                }
                return ob_get_clean();
                break;
        }
//
    }

    public function __get($varName)
    {
        return $this->data[$varName] ?? null;
    }
}
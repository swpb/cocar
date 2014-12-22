<?php
/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 22/12/14
 * Time: 13:47
 */

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Printer
 *
 * @ORM\Table("tb_printer_models")
 * @ORM\Entity(repositoryClass="Swpb\Bundle\CocarBundle\Entity\PrinterModelsRepository")
 */
class PrinterModels {

    /**
     * Construct
     */
    public function __construct() {

    }

    /**
     * @var integer
     *
     * @ORM\Column(name="model_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $model_id;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", unique=true)
     */
    private $model;

    /**
     * @return int
     */
    public function getModelId() {
        return $this->model_id;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model) {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function getModel() {
        return $this->model;
    }

}
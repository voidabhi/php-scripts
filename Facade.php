<?php
namespace app\components\facades;
use Yii;
abstract class Facade
{
  
  /**
   * get the app component name
   * @return string
   */
  abstract protected static function getComponentName();
  /**
   * get the app component instance
   * @return Component/Object instance
   */
  protected static function provideComponent()
  {
    $name = static::getComponentName();
    return Yii::$app->get($name);
  }
   /**
   * get the app component instance
   * @return Component/Object instance
   */
  public static function loadComponentInstance()
  {
    return static::provideComponent();
  }
  /**
   * magic method static calls to the object's method.
   *
   * @param  string  $method
   * @param  array   $args
   * @return mixed
   *
   */
  public static function __callStatic($method,$params)
  {
    $component = static::loadComponentInstance();
    return call_user_func_array([$component, $method], $params);
  }
  
}

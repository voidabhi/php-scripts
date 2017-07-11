<?php

abstract class Animals
{
    const LION = 0;
    const DEER = 1;
    const VULTURE = 2;
    const HUMMINGBIRD = 3;
}

public class MammalFactory extends AnimalFactory()
{
    protected function createAnimal($carnivore)
    {
        return $carnivore === Animals::LION ?
            new Lion() : new Deer(); 
    } 
}

public class BirdFactory extends AnimalFactory()
{
    protected function createAnimal($carnivore)
    {
        return $carnivore === Animals::VULTURE ?
            new Vulture() : new Hummingbird(); 
    } 
}


$mammalFactory = new MammalFactory();
$birdFactory = new BirdFactory();
$lion = $mammalFactory->createAnimal(Animals::LION);
$lion->breathe();
$hummingbird = $birdFactory->createAnimal(Animals::HUMMINGBIRD);
$hummingbird->breathe();

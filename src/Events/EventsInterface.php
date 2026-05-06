<?php

namespace Atusan\Events;

interface EventsInterface
{
  public function onInit();

  public function onOpen();

  public function onClose();
}

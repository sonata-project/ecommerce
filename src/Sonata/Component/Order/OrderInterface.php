<?php


namespace Sonata\Component\Order;

interface OrderInterface {


    const STATUS_OPEN       = 0; // created but not validated
    const STATUS_PENDING    = 1; // waiting from action from the user
    const STATUS_VALIDATED  = 2; // the order is validated does not mean the payment is ok
    const STATUS_CANCELLED  = 3; // the order is cancelled
    const STATUS_ERROR      = 4; // the order has an error
    const STATUS_STOPPED    = 5; // use if the subscription has been cancelled/stopped


}
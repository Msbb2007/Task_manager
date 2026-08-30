<?php

namespace model\mainClasses;

enum priority : string{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
}
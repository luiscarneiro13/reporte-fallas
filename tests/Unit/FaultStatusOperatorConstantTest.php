<?php

namespace Tests\Unit;

use App\Models\FaultStatus;
use PHPUnit\Framework\TestCase;

/**
 * El nombre del status "operador" es compartido por FaultController::create()
 * y FaultRequest::rules() a través de esta constante. Si alguien la renombra
 * sin actualizar ambos lados, el flujo de creación de fallas para el rol
 * Operador se rompe silenciosamente (deja de encontrar/crear el status fijo).
 */
class FaultStatusOperatorConstantTest extends TestCase
{
    public function test_operator_status_name_no_ha_cambiado_de_forma_inadvertida(): void
    {
        $this->assertSame('Por programación interna', FaultStatus::OPERATOR_STATUS_NAME);
    }
}

<?php

namespace Tests\Unit;

use App\Enums\RolUsuario;
use PHPUnit\Framework\TestCase;

class RolUsuarioTest extends TestCase
{
    public function test_admin_puede_gestionar_club_y_usuarios(): void
    {
        // Arrange
        $rol = RolUsuario::Admin;

        // Act
        $puedeGestionarClub = $rol->puedeGestionarClub();
        $puedeGestionarUsuarios = $rol->puedeGestionarUsuarios();

        // Assert
        $this->assertTrue($puedeGestionarClub);
        $this->assertTrue($puedeGestionarUsuarios);
    }

    public function test_entrenador_admin_puede_gestionar_club_pero_no_usuarios(): void
    {
        // Arrange
        $rol = RolUsuario::EntrenadorAdmin;

        // Act
        $puedeGestionarClub = $rol->puedeGestionarClub();
        $puedeGestionarUsuarios = $rol->puedeGestionarUsuarios();

        // Assert
        $this->assertTrue($puedeGestionarClub);
        $this->assertFalse($puedeGestionarUsuarios);
    }

    public function test_entrenador_no_puede_gestionar_club_ni_usuarios(): void
    {
        // Arrange
        $rol = RolUsuario::Entrenador;

        // Act
        $puedeGestionarClub = $rol->puedeGestionarClub();
        $puedeGestionarUsuarios = $rol->puedeGestionarUsuarios();

        // Assert
        $this->assertFalse($puedeGestionarClub);
        $this->assertFalse($puedeGestionarUsuarios);
    }
}
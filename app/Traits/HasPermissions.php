<?php

namespace App\Traits;

use App\Enums\UserRole;

trait HasPermissions
{
    /**
     * Check if user can manage school configuration.
     */
    public function canManageSchool(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if user can manage users.
     */
    public function canManageUsers(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if user can manage students.
     */
    public function canManageStudents(): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::SECRETARY]);
    }

    /**
     * Check if user can manage finances.
     */
    public function canManageFinances(): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::SECRETARY]);
    }

    /**
     * Check if user can enter grades for all classes.
     */
    public function canEnterAllGrades(): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::SECRETARY]);
    }

    /**
     * Check if user can validate report cards.
     */
    public function canValidateReportCards(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if user can generate documents.
     */
    public function canGenerateDocuments(): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::SECRETARY]);
    }
}

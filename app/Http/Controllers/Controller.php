<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Check if the current user can edit a document.
     * Staff can only edit their own documents, admins can edit all.
     */
    protected function canEditDocument($document)
    {
        return $this->canDeleteDocument($document);
    }

    /**
     * Check if the current user can delete a document.
     * Staff can only delete their own documents, admins can delete all.
     */
    protected function canDeleteDocument($document)
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->role === 'admin') {
            return true;
        }

        $ownerId = $document instanceof \App\Models\Accomplishment
            ? $document->uploaded_by
            : $document->added_by;

        return $user->role === 'staff'
            && $ownerId !== null
            && (string) $ownerId === (string) $user->getKey();
    }
}

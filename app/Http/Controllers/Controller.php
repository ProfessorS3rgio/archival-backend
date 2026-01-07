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
        $user = auth()->user();
        
        // Admin can edit all documents
        if ($user->role === 'admin') {
            return true;
        }
        
        // Staff can only edit documents they created
        if ($user->role === 'staff' && $document->added_by === $user->id) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if the current user can delete a document.
     * Staff can only delete their own documents, admins can delete all.
     */
    protected function canDeleteDocument($document)
    {
        return $this->canEditDocument($document);
    }
}

<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AffiliateDomainResolver;

class UserDomainObserver
{
    public function saving(User $user): void
    {
        $resolver = app(AffiliateDomainResolver::class);
        foreach (['dominio', 'dominio_externo'] as $field) {
            if ($user->isDirty($field)) {
                $user->{$field} = $resolver->normalize($user->{$field}) ?: null;
            }
        }
    }

    public function created(User $user): void
    {
        $this->invalidateAfterCommit($user);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged(['dominio', 'dominio_externo'])) {
            $this->invalidateAfterCommit($user);
        }
    }

    public function deleted(User $user): void
    {
        $this->invalidateAfterCommit($user);
    }

    private function invalidateAfterCommit(User $user): void
    {
        // Capture the originals before Eloquent synchronizes the saved attributes.
        $domains = [
            $user->getRawOriginal('dominio'),
            $user->getRawOriginal('dominio_externo'),
            $user->dominio,
            $user->dominio_externo,
        ];
        $invalidate = fn () => app(AffiliateDomainResolver::class)->forget($domains);

        if ($user->getConnection()->transactionLevel() > 0) {
            $user->getConnection()->afterCommit($invalidate);
        } else {
            $invalidate();
        }
    }
}

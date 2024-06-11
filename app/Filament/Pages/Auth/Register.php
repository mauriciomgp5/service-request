<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Page;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as AuthRegister;

class Register extends AuthRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data')
            ),
        ];
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
        return $data;
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->mask('(99) 99999-9999')
            ->placeholder('(99) 99999-9999')
            ->label('Celular')
            ->unique($this->getUserModel(), 'phone', modifyRuleUsing: function (Unique $rule, $state) {
                return $rule->where('phone', preg_replace('/[^0-9]/', '', $state));
            })
            ->required()
            ->autofocus();
    }

    protected function beforeValidate(): void
    {
        $this->data['phone'] = preg_replace('/[^0-9]/', '', $this->data['phone']);
    }

    protected function afterValidate(): void
    {
        //mask phone
        $this->data['phone'] = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $this->data['phone']);
        dd($this->data);
    }
}

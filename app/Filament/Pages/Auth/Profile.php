<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\EditProfile;

class Profile extends EditProfile
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
                    ->statePath('data'),
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
            ->label('Celular')
            ->unique(modifyRuleUsing: function ($rule, $state) {
                dd($rule, $state);

                return $rule->where('column', 'phone');
            })
            ->required()
            ->autofocus();
    }
}

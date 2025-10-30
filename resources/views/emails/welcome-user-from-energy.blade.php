@component('mail::message')
# Bem-vindo à plataforma MyCarbu

Olá {{ $user->name }},

A sua conta foi criada com sucesso na plataforma MyCarbu. Já pode iniciar sessão e começar a utilizar os nossos serviços.

@component('mail::panel')
**Email:** {{ $user->email }}  
**Palavra-passe temporária:** `{{ $password }}`
@endcomponent

@component('mail::button', ['url' => route('login')])
Iniciar sessão
@endcomponent

Por favor, altere a sua palavra-passe após o primeiro acesso por motivos de segurança.

Se tiver alguma dúvida, contacte o suporte.

Cumprimentos,  
{{ config('app.name') }}
@endcomponent


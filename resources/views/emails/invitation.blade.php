@component('mail::message')
# Seu acesso ao sistema SIG-CEPROESC

Olá, {{ $invitation->registration->name }}!

Esse é o e-mail de acesso para você se cadastrar no sistema SIG do Ceproesc.

Clique no botão abaixo para ir até a página de cadastro, criar sua senha e ter acesso ao sistema.

@component('mail::button', ['url' => route('invitations.show', ['code' => $invitation->code])])
Acessar o sistema
@endcomponent

Para qualquer dúvida, a equipe do Ceproesc está à sua disposição.<br>
{{ config('app.name') }}
@endcomponent

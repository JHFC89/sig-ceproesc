@if ($show)

    <x-lesson.for-today-list title="aulas de hoje" :hideRegistered="true" :alwaysShow="false" :user="request()->user()"/>

    <x-lesson.for-week-list title="aulas da semana" :hideRegistered="true" :user="request()->user()"/>

@endif

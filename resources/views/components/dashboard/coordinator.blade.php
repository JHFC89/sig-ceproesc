@if ($show)

    <x-lesson.for-today-list title="aulas de hoje" :hideRegistered="true" :alwaysShow="false" :user="request()->user()"/>

    <x-lesson.for-week-list title="aulas da semana" :hideRegistered="true" :alwaysShow="false" :user="request()->user()"/>

    @if ($requests->count() > 0)
    <div class="space-y-8 lg:space-y-0 lg:flex lg:space-x-8">
        <x-dashboard.requests :requests="$requests" class="lg:w-1/2"/>
    </div>
    @endif

@endif

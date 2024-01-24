<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ 
        state: $wire.$entangle('{{ $getStatePath() }}'),
        youtubeId : null,
     }">
        <input
            x-model="state"
            class="w-full"
            x-on:input=" () => {
                youtubeId = null;
                const url = state;
                const regex = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
                const match = url.match(regex);
                if (match && match[1]) {
                    youtubeId = match[1];
                }else {
                    youtubeId = null;
                }
            }
            "
        />

        <template x-if="youtubeId">
            <div class="my-4">
                <iframe
                    width="100%"
                    x-bind:src="`https://www.youtube.com/embed/${youtubeId}`"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>
            </div>
        </template>
    </div>
</x-dynamic-component>
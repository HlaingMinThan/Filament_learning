<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ 
        state: $wire.$entangle('{{ $getStatePath() }}'),
        checkAndUpdateYoutubeId() {
            const url = this.state;
            const regex = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
            const match = url.match(regex);
            this.state = match && match[1] ? match[1] : null;
            //check if youtube video is playable,if not,set utube id null
        }
     }">
        <input
            style="border:1px solid rgba(211, 211, 211, 0.694)"
            x-model="state"
            class="w-full rounded-xl text-sm"
            x-on:input=" () => {
                this.state = null;
                checkAndUpdateYoutubeId()
            }
            "
        />

        <template x-if="state">
            <div class="my-4">
                <iframe
                    width="100%"
                    x-bind:src="`https://www.youtube.com/embed/${state}`"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>
            </div>
        </template>
    </div>
</x-dynamic-component>
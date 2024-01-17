<x-feadmin::layouts.panel>
    <div id="appearance-editor" class="fd-flex fd-h-full">
        <div class="fd-shrink-0 fd-w-80 fd-h-full fd-bg-[#1e1e1e] fd-border-r fd-border-zinc-700 fd-px-3 fd-pt-3 fd-pb-40 fd-overflow-auto">
            @foreach ($files as $directory => $childFiles)
                <x-feadmin::appearance.editor.directory
                        :requestedFile="$requestedFile"
                        :directory="$directory"
                        :files="$childFiles"
                />
            @endforeach
        </div>
        <x-feadmin::form
                id="editor-form"
                :action="panel_route('appearance.editor.update')"
                method="PUT"
                class="fd-w-full fd-flex fd-flex-col"
        >
            <input type="hidden" name="file" value="{{ $requestedFile->getRelativePathname() }}">
            <div class="fd-px-4 fd-py-2 fd-text-sm fd-font-medium fd-bg-[#1e1e1e] fd-text-zinc-200">
                {{ $filePath }}
            </div>
            <x-feadmin::form.group name="content" class="fd-w-full fd-h-full">
                <x-feadmin::form.code-editor
                        :default="$requestedFile->getContents()"
                        :data-code-editor="json_encode(['language' => $requestedFile->getExtension()])"
                        class="fd-h-full"
                />
            </x-feadmin::form.group>
            <x-feadmin::form.sticky-submit />
        </x-feadmin::form>
    </div>
    @push('after_scripts')
        @vite('resources/js/code-editor.js', 'feadmin/build')
        @if ($errors->any())
            <script>
              document.addEventListener("DOMContentLoaded", function() {
                Feadmin.Toastr.add(@json($errors->first()));
              });
            </script>
        @endif

        <script>
          window.addEventListener("keydown", function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === "s") {
              e.preventDefault();

              const form = document.getElementById("editor-form");
              form.submit();

              Feadmin.Form.handleSubmitters(form);
            }
          });
        </script>
    @endpush
</x-feadmin::layouts.panel>
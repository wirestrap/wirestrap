<x-layouts.app>
    <div class="p-4">
        {{-- Basic menu with groups --}}
        <x-wirestrap::menu id="menu-basic">
            <x-slot:home>
                <a href="#">Home</a>
            </x-slot:home>

            <x-slot:settings_group>
                <x-wirestrap::menu.accordion id="settings" label="Settings">
                    <x-slot:general><a href="#">General</a></x-slot:general>
                    <x-slot:security><a href="#">Security</a></x-slot:security>
                </x-wirestrap::menu.accordion>
            </x-slot:settings_group>

            <x-slot:reports_group>
                <x-wirestrap::menu.accordion id="reports" label="Reports" open>
                    <x-slot:monthly><a href="#">Monthly</a></x-slot:monthly>
                    <x-slot:annual><a href="#">Annual</a></x-slot:annual>
                </x-wirestrap::menu.accordion>
            </x-slot:reports_group>
        </x-wirestrap::menu>

        <br>

        {{-- Single mode --}}
        <x-wirestrap::menu id="menu-single" :single="true">
            <x-slot:group_a>
                <x-wirestrap::menu.accordion id="grp-a" label="Group A" open>
                    <x-slot:item_a><a href="#">Item A</a></x-slot:item_a>
                </x-wirestrap::menu.accordion>
            </x-slot:group_a>

            <x-slot:group_b>
                <x-wirestrap::menu.accordion id="grp-b" label="Group B">
                    <x-slot:item_b><a href="#">Item B</a></x-slot:item_b>
                </x-wirestrap::menu.accordion>
            </x-slot:group_b>
        </x-wirestrap::menu>

        <br>

        {{-- Events for programmatic control --}}
        <x-wirestrap::menu id="menu-events" :events="true">
            <x-slot:ev_group>
                <x-wirestrap::menu.accordion id="ev-grp" label="Events group">
                    <x-slot:ev_item><a href="#">Event item</a></x-slot:ev_item>
                </x-wirestrap::menu.accordion>
            </x-slot:ev_group>
        </x-wirestrap::menu>

        <br>

        {{-- Nesting --}}
        <x-wirestrap::menu id="menu-nested">
            <x-slot:settings_group>
                <x-wirestrap::menu.accordion id="nest-settings" label="Settings" open>
                    <x-slot:general><a href="#">General</a></x-slot:general>
                    <x-slot:advanced_group>
                        <x-wirestrap::menu.accordion id="nest-advanced" label="Advanced">
                            <x-slot:api><a href="#">API</a></x-slot:api>
                            <x-slot:webhooks><a href="#">Webhooks</a></x-slot:webhooks>
                        </x-wirestrap::menu.accordion>
                    </x-slot:advanced_group>
                </x-wirestrap::menu.accordion>
            </x-slot:settings_group>
        </x-wirestrap::menu>

        <br>

        {{-- Nesting + single mode --}}
        <x-wirestrap::menu id="menu-nested-single" :single="true">
            <x-slot:settings_group>
                <x-wirestrap::menu.accordion id="ns-settings" label="Settings" open>
                    <x-slot:general><a href="#">General</a></x-slot:general>
                    <x-slot:advanced_group>
                        <x-wirestrap::menu.accordion id="ns-advanced" label="Advanced">
                            <x-slot:api><a href="#">API</a></x-slot:api>
                        </x-wirestrap::menu.accordion>
                    </x-slot:advanced_group>
                    <x-slot:security_group>
                        <x-wirestrap::menu.accordion id="ns-security" label="Security">
                            <x-slot:twofa><a href="#">2FA</a></x-slot:twofa>
                        </x-wirestrap::menu.accordion>
                    </x-slot:security_group>
                </x-wirestrap::menu.accordion>
            </x-slot:settings_group>
        </x-wirestrap::menu>

        <br>

        {{-- Nesting + events (magic opens ancestors) --}}
        <x-wirestrap::menu id="menu-nested-events" :events="true">
            <x-slot:settings_group>
                <x-wirestrap::menu.accordion id="ne-settings" label="Settings">
                    <x-slot:advanced_group>
                        <x-wirestrap::menu.accordion id="ne-advanced" label="Advanced">
                            <x-slot:api><a href="#">API</a></x-slot:api>
                        </x-wirestrap::menu.accordion>
                    </x-slot:advanced_group>
                </x-wirestrap::menu.accordion>
            </x-slot:settings_group>
        </x-wirestrap::menu>
    </div>
</x-layouts.app>

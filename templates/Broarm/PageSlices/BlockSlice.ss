<% if $Blocks %>
    <section class="page-slice block-slice">
        <div class="grid-container block-slice__container">
            <div class="grid-x grid-padding-x medium-up-{$Blocks.Count}">
                <% loop $Blocks %>
                    <div class="cell">
                        $Template
                    </div>
                <% end_loop %>
            </div>
        </div>
    </section>
<% end_if %>
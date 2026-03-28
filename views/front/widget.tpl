{if !empty($magix_faqmulti_data.items)}
    {* 🟢 Injection du JSON-LD (Schema FAQPage) pour le SEO Google *}
    {$magix_faqmulti_data.seo nofilter}

    <div class="magix-faqmulti-widget my-5">

        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-patch-question fs-2 text-primary me-3"></i>
            <h3 class="h4 mb-0 fw-bold">{#faq_title#|default:'Foire Aux Questions'}</h3>
        </div>

        <div class="accordion shadow-sm" id="faqAccordion_{$magix_faqmulti_data.module}">

            {foreach $magix_faqmulti_data.items as $faq}
                {* Identifiants uniques pour le fonctionnement de l'accordéon Bootstrap *}
                {assign var="headingId" value="faqHeading_{$magix_faqmulti_data.module}_{$faq.id_faqmulti}"}
                {assign var="collapseId" value="faqCollapse_{$magix_faqmulti_data.module}_{$faq.id_faqmulti}"}

                {* On ouvre le tout premier élément de la liste par défaut *}
                {assign var="isOpen" value=$faq@first}

                <div class="accordion-item border-0 border-bottom">

                    <h2 class="accordion-header" id="{$headingId}">
                        <button class="accordion-button fw-medium {if !$isOpen}collapsed{/if} bg-transparent"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#{$collapseId}"
                                aria-expanded="{if $isOpen}true{else}false{/if}"
                                aria-controls="{$collapseId}">
                            {$faq.title_faqmulti|escape:'html'}
                        </button>
                    </h2>

                    <div id="{$collapseId}"
                         class="accordion-collapse collapse {if $isOpen}show{/if}"
                         aria-labelledby="{$headingId}"
                         data-bs-parent="#faqAccordion_{$magix_faqmulti_data.module}">

                        <div class="accordion-body text-muted bg-light">
                            {* nofilter est vital ici car la réponse est formatée via TinyMCE *}
                            {$faq.desc_faqmulti nofilter}
                        </div>

                    </div>
                </div>
            {/foreach}

        </div>
    </div>
{/if}
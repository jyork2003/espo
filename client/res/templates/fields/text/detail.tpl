{{#if isNotEmpty}}
<div
    class="complex-text-container{{#if isCut}} cut{{/if}}"
    {{#if cutHeight}} style="max-height: {{cutHeight}}px;"{{/if}}
>
    <div class="complex-text">{{#unless displayRawText}}{{complexText value}}{{else}}{{breaklines value}}{{/unless}}</div>
</div>
{{#if isCut}}
<div class="see-more-container hidden">
    <a href="javascript:" data-action="seeMoreText">{{translate 'See more'}}</a>
</div>
{{/if}}
{{else}}
    {{#if valueIsSet}}<span class="none-value">{{translate 'None'}}</span>{{else}}
    <span class="loading-value">...</span>{{/if}}
{{/if}}

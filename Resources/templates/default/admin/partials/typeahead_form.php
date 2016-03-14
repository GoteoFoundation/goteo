<form style="<?= $this->hidden ? 'display:none;': '' ?>">
    <div class="typeahead-container">
        <div class="typeahead-field">

            <span class="typeahead-query">
                <input id="<?= $this->id ? $this->id: 'typeahead-form' ?>"
                       name="<?= $this->name ? $this->name : ($this->id ? $this->id : 'q') ?>"
                       <?= ($this->value ? 'value="' . $this->value .'"' : '') ?>
                       type="search"
                       autocomplete="off">
            </span>
<!--             <span class="typeahead-button">
                <button type="submit">
                    <span class="typeahead-search-icon"></span>
                </button>
            </span>
 -->
        </div>
    </div>
</form>

    <div class="paginator">
        <ul class="pagination">
			<?= $this->Paginator->first('Début'); ?>
            <?= $this->Paginator->hasPrev() ? $this->Paginator->prev('< ' . __('précédent')) : '' ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->hasNext() ? $this->Paginator->next(__('suivant') . ' >') : '' ?>
			<?= $this->Paginator->last('Fin'); ?>
        </ul>
        <p><?= $this->Paginator->counter('{{page}} sur {{pages}}') ?></p>
    </div>	
	
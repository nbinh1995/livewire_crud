<?php

namespace App\Http\Livewire;

use App\Models\Page;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Pages extends Component
{
    use WithPagination;
    protected const PAGINATION_PER_PAGE = 3;
    public $modalFormVisible = false;
    public $modalConfirmDelete = false;
    public $modal_id;
    /**
     * slug,title,content attribute model page
     *
     * @var mixed
     */
    public $slug;
    public $title;
    public $content;

    /**
     * rules validate
     *
     * @return void
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'slug' => ['required', Rule::unique('pages', 'slug')],
            'content' => 'required',
        ];
    }

    public function mount()
    {
        $this->resetPage();
    }

    public function read()
    {
        return Page::paginate(self::PAGINATION_PER_PAGE);
    }
    /**
     * messages validate
     *
     * @return void
     */
    public function messages()
    {
        return [
            'title.required' => 'Không được để trống Title!',
            'slug.required' => 'Không được để trống Slug!',
            'slug.unique' => 'slug đã có trông dữ liệu',
            'content.required' => 'Không được để trống Content!',
        ];
    }

    /**
     * Auto generate slug Title
     *
     * @param  mixed $value
     * @return void
     */
    public function updatedTitle($value)
    {
        $this->generateSlug($value);
    }

    /**
     * create Page
     *
     * @return void
     */
    public function create()
    {
        $this->validate();
        Page::create($this->modelData());
        $this->modalFormVisible = false;
    }

    public function update()
    {
        $this->validate();
        Page::find($this->modal_id)->update($this->modelData());
        $this->modalFormVisible = false;
    }

    public function delete()
    {
        Page::destroy($this->modal_id);
        $this->modalConfirmDelete = false;
        $this->resetPage();
    }

    /**
     * show Form Modal
     *
     * @return void
     */
    public function createShowModal()
    {
        $this->resetForm();
        $this->modalFormVisible = true;
    }

    public function updateShowModal($id)
    {
        $this->resetForm();
        $this->modal_id = $id;
        $this->modalFormVisible = true;
        $this->loadModal();
    }

    public function deleteShowModal($id)
    {
        $this->modal_id = $id;
        $this->modalConfirmDelete = true;
    }

    public function loadModal()
    {
        $page = Page::find($this->modal_id);
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content;
    }
    /**
     * data binding component
     *
     * @return void
     */
    protected function modelData()
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content
        ];
    }

    protected function resetForm()
    {
        $this->resetValidation();
        $this->resetVars();
    }

    /**
     * reset Data to null
     *
     * @return void
     */
    protected function resetVars()
    {
        $this->modal_id = null;
        $this->title = null;
        $this->slug = null;
        $this->content = null;
    }


    /**
     * generate title Slug
     *
     * @param  mixed $value
     * @return void
     */
    protected function generateSlug($value)
    {
        $process_2 = utf8Slug($value);
        $this->slug = $process_2;
    }
    public function render()
    {
        return view('livewire.pages', ['pages' => $this->read()]);
    }
}

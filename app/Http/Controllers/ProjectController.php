<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Category;
use ProtoneMedia\Splade\Facades\SEO;
use ProtoneMedia\Splade\FormBuilder\Checkbox;
use ProtoneMedia\Splade\FormBuilder\Datetime;
use ProtoneMedia\Splade\FormBuilder\Select;
use ProtoneMedia\Splade\FormBuilder\Submit;
use ProtoneMedia\Splade\SpladeForm;
use ProtoneMedia\Splade\SpladeTable;
use ProtoneMedia\Splade\Facades\Toast;
use App\Http\Requests\StoreProjectRequest;
use ProtoneMedia\Splade\FormBuilder\Checkboxes;
use ProtoneMedia\Splade\FormBuilder\File;
use ProtoneMedia\Splade\FormBuilder\Input;

class ProjectController extends Controller
{
    public function index()
    {
        return view('projects.index', [
            'projects' => SpladeTable::for(Project::class)
                ->column('id')
                ->column('name')
                ->column('actions')
                ->paginate(15),
        ]);
    }

    public function create()
    {
        // $categories = Category::pluck('name', 'id');
        // $users = User::pluck('name', 'id');

        // return view('projects.create', compact('categories', 'users'));
                $form = SpladeForm::make()
            ->action(route('projects.store'))
            ->fields([
                Input::make('name')->label('Name'),
                // Datetime::make('date')->label('start_date'),
                Datetime::make('start_date')->label('Start Date'),
                Select::make('catgegory_id')
                ->options(Category::pluck('name', 'id')->toArray())
                ->label('Choose Category')
                    // ->multiple()    // Enables choosing multiple options
                    ->choices(),
                Checkbox::make('is_active')->label('Is Active')->value(true),
                File::make('logo')->label('logo')
                ->filepond()
                ->preview(),
                Checkboxes::make('users')
                ->options(User::pluck('name', 'id')->toArray())
                ->inline()
                ->label('Choose User'),
                Submit::make()->label('Submit')->danger(),
            ]);

        return view('projects.create', [
            'form' => $form,
        ]);

    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $data['logo'] = $request->file('logo')->store('logos');
        $project = Project::create($data);

        $project->users()->attach($request->users);

        Toast::title('Project saved');

        return redirect()->route('projects.index');
    }

    public function edit(Project $project)
    {
        // SEO::title($project->name);

        // $categories = Category::pluck('name', 'id');
        // $users = User::pluck('name', 'id');

        // return view('projects.edit', compact('project', 'categories', 'users'));

                $form = SpladeForm::make()
            ->action(route('projects.store'))
            ->fields([
                Input::make('name')->label('Name'),
                // Datetime::make('date')->label('start_date'),
                Datetime::make('start_date')->label('Start Date'),
                Select::make('catgegory_id')
                    ->options(Category::pluck('name', 'id')->toArray())
                    ->label('Choose Category')
                    // ->multiple()    // Enables choosing multiple options
                    ->choices(),
                Checkbox::make('is_active')->label('Is Active')->value(true),
                File::make('logo')->label('logo')
                    ->filepond()
                    ->preview(),
                Checkboxes::make('users')
                    ->options(User::pluck('name', 'id')->toArray())
                    ->inline()
                    ->label('Choose User'),
                Submit::make()->label('Submit')->danger(),
            ])
            ->fill($project);

        return view('projects.edit', [
            'form' => $form,
        ]);
    }

    public function update(Project $project, StoreProjectRequest $request)
    {
        $data = $request->validated();
        $data['logo'] = $request->file('logo')->store('logos');
        $project->update($data);

        $project->users()->sync($request->users);

        Toast::title('Project saved');

        return redirect()->route('projects.index');
    }

    public function destroy(Project $project)
    {

        $project->users()->detach();
        $project->delete();

        return redirect()->route('projects.index');
    }

}

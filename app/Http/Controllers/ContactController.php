<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Models\Contact;
use App\Models\TaxRate;


class ContactController extends BaseController
{
    protected $model = Contact::class;

    protected $create_request = ContactCreateRequest::class;

    protected $update_request = ContactUpdateRequest::class;

    public function store()
    {
        $this->authorize('create', $this->model);

        if (isset($this->create_request)) {
            $request = (new $this->create_request);

            request()->validate($request->rules(), $request->messages());
        }

        $data = request()->all();

        return $this->transact(function () use ($data) {
            $contact = $this->model::create($data);

            if (optional($data)['contact_persons']) {
                foreach ($data['contact_persons'] as $contact_person) {
                    $contact_person = $contact->contactPersons()->create($contact_person);
                }
            }

            return $contact->load('contactPersons');
        }, "RESOURCE_CREATED", "RESOURCE_NOT_CREATED");
    }

    public function update($id)
    {
        $this->authorize('update', $this->model);

        if (isset($this->update_request)) {
            $request = (new $this->update_request);

            request()->validate($request->rules(), $request->messages());
        }

        $data = request()->all();

        return $this->transact(function () use ($id, $data) {
            $contact = $this->model::find($id);

            $contact->update($data);

            if (optional($data)['contact_persons']) {
                foreach ($data['contact_persons'] as $contact_person) {
                    if (optional($contact_person)['id']) {
                        $contact_person = $contact->contactPersons()
                            ->find($contact_person['id'])
                            ->update($contact_person);
                    } else {
                        $contact_person = $contact->contactPersons()->create($contact_person);
                    }
                }
            }

            return $contact->load('contactPersons');
        }, "RESOURCE_UPDATED", "RESOURCE_NOT_UPDATED");
    }

    public function taxTypes()
    {
        return $this->resolve(config('company.tax_types'));
    }

    public function dueDateTypes()
    {
        return $this->resolve(config('company.due_date_types'));
    }
}

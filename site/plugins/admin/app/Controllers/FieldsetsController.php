<?php

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Text\Text;
use function Flextype\Component\I18n\__;

class FieldsetsController extends Controller
{
   public function index($request, $response, $args)
   {
       return $this->view->render($response,
                                  'plugins/admin/views/templates/extends/fieldsets/index.html', [
           'menu_item' => 'fieldsets',
           'fieldsets_list' => $this->fieldsets->fetchList(),
           'links' =>  [
                            'fieldsets' => [
                                'link' => $this->router->pathFor('admin.fieldsets.index'),
                                'title' => __('admin_fieldsets'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ],
            'buttons' => [
                            'fieldsets_add' => [
                                'link' => $this->router->pathFor('admin.fieldsets.add'),
                                'title' => __('admin_create_new_fieldset'),
                                'attributes' => ['class' => 'float-right btn']
                            ]
                         ]
       ]);
   }

   public function add($request, $response, $args)
   {
       return $this->view->render($response,
                                  'plugins/admin/views/templates/extends/fieldsets/add.html', [
           'menu_item' => 'fieldsets',
           'fieldsets_list' => $this->fieldsets->fetchList(),
           'links' =>  [
                            'fieldsets' => [
                                'link' => $this->router->pathFor('admin.fieldsets.index'),
                                'title' => __('admin_fieldsets'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ],
            'buttons' => [
                            'fieldsets_add' => [
                                'link' => $this->router->pathFor('admin.fieldsets.add'),
                                'title' => __('admin_create_new_fieldset'),
                                'attributes' => ['class' => 'float-right btn']
                            ]
                         ]
       ]);
   }

   public function addProcess($request, $response, $args)
   {
        $data = $request->getParsedBody();

        Arr::delete($data, 'csrf_name');
        Arr::delete($data, 'csrf_value');

        $id = Text::safeString($data['id'], '-', true);
        $data = ['title' => $data['title']];

        if ($this->fieldsets->create($id, $data)) {
            $this->flash->addMessage('success', __('admin_message_fieldset_created'));
        } else {
            $this->flash->addMessage('error', __('admin_message_fieldset_was_not_created'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.fieldsets.index'));
   }

   public function edit($request, $response, $args)
   {
       return $this->view->render($response,
                                  'plugins/admin/views/templates/extends/fieldsets/edit.html', [
           'menu_item' => 'fieldsets',
           'id' => $request->getQueryParams()['id'],
           'fieldsets_body' => $this->fieldsets->fetch($request->getQueryParams()['id']),
           'links' =>  [
                            'fieldsets' => [
                                'link' => $this->router->pathFor('admin.fieldsets.edit') . '?id=' . $request->getQueryParams()['id'],
                                'title' => __('admin_fieldsets'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ],
            'buttons' => [
                            'save_entry' => [
                                'link' => 'javascript:;',
                                'title' => __('admin_save'),
                                'attributes' => ['class' => 'js-save-form-submit float-right btn']
                            ],
                        ]
       ]);
   }

   public function editProcess($request, $response, $args)
   {

   }

   public function rename($request, $response, $args)
   {
       return $this->view->render($response,
                                  'plugins/admin/views/templates/extends/fieldsets/rename.html', [
           'menu_item' => 'fieldsets',
           'id' => $request->getQueryParams()['id'],
           'links' =>  [
                            'fieldsets' => [
                                'link' => $this->router->pathFor('admin.fieldsets.index'),
                                'title' => __('admin_fieldsets'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                            'fieldsets_rename' => [
                                'link' => $this->router->pathFor('admin.fieldsets.rename') . '?id=' . $request->getQueryParams()['id'],
                                'title' => __('admin_rename'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ],
       ]);
   }

   public function renameProcess($request, $response, $args)
   {
       if ($this->fieldsets->rename($request->getParsedBody()['fieldset-id-current'], $request->getParsedBody()['id'])) {
           $this->flash->addMessage('success', __('admin_message_fieldset_renamed'));
       } else {
           $this->flash->addMessage('error', __('admin_message_fieldset_was_not_renamed'));
       }

       return $response->withRedirect($this->container->get('router')->pathFor('admin.fieldsets.index'));

   }

   public function deleteProcess($request, $response, $args)
   {
       if ($this->fieldsets->delete($request->getParsedBody()['fieldset-id'])) {
           $this->flash->addMessage('success', __('admin_message_fieldset_deleted'));
       } else {
           $this->flash->addMessage('error', __('admin_message_fieldset_was_not_deleted'));
       }

       return $response->withRedirect($this->container->get('router')->pathFor('admin.fieldsets.index'));
   }

   public function duplicateProcess($request, $response, $args)
   {
       if ($this->fieldsets->copy($request->getParsedBody()['fieldset-id'], $request->getParsedBody()['fieldset-id'] . '-duplicate-' . date("Ymd_His"))) {
           $this->flash->addMessage('success', __('admin_message_fieldset_duplicated'));
       } else {
           $this->flash->addMessage('error', __('admin_message_fieldset_was_not_duplicated'));
       }

       return $response->withRedirect($this->container->get('router')->pathFor('admin.fieldsets.index'));
   }
}
<?php

namespace Anax\Comments;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable,
        \Anax\MVC\TRedirectHelpers; 


    /**
     * Initialize model.
     *
     * @return void
     */
    public function initialize()
    {
        $this->comment = new \Anax\Comments\Comment();
        $this->comment->setDI($this->di);
    }


    public function listAction($type, $typeId) {
        $this->initialize();

        // $sql = "
        //     SELECT *
        //     FROM prj_comment
        //     JOIN prj_userview ON prj_comment.userid = prj_userview.id
        //     WHERE prj_comment.type = ? AND prj_comment.typeid = ?
        // ";
        // $comments = $this->comment->executeRaw($sql, [$type, $typeId]);

        $comments = $this->comment->query()
            ->join('userview', 'prj_comment.userid = prj_userview.id')
            ->where('type = ?')
            ->andWhere('typeid = ?')
            ->execute([$type, $typeId]);

        $this->views->add('comment/list-all', [
            'comments'  => $comments
        ]);
    }


    /**
    * Add a comment.
    *
    * @return void
    */
    public function addAction($type, $typeId)
    {
        $this->initialize();

        // Get loggedin user
        $user = $this->AuthController->getLoggedInUser();

        $form = $this->di->form;
        $form->create([], [
            'content' => [
                'type'          => 'text',
                'required'      => true,
                'label'         => false,
                'validation'    => ['not_empty'],
            ],
            'reset' => [
                'type'          => 'reset',
                'value'         => 'Reset',
                'class'         => 'red',
                'callback'      => null, // Reset button, no function needed
            ],
            'submit' => [
                'type'          => 'submit',
                'class'         => 'red',
                'value'         => 'Comment',
                'name'          => 'submit-' . $type . '-' . $typeId,
                'callback'      => function ($form) use ($type, $typeId, $user) {
                    
                    $now        = gmdate('Y-m-d H:i:s');
                    $content    = $this->di->textFilter->doFilter(htmlentities($form->value('content'), null, 'UTF-8'), 'markdown');
                   
                    // Save user input to database
                    $save = $this->comment->save([
                        'type'      => $type,
                        'typeId'    => $typeId,
                        'userId'    => $user['id'],
                        'created'   => $now,
                        'content'   => $content,
                    ]);
                    $form->saveInSession = false;

                    return $save;    
                }
            ]
        ]);

        $callbackSuccess = function ($form) {
            $this->redirectTo();
        };
         
        $callbackFail = function ($form) {
            $this->di->sparkles->flash('error', 'Comment failed...');
            $this->redirectTo();
        };
         
        // Check the status of the form
        $form->check($callbackSuccess, $callbackFail);

        $this->views->add('comment/comment-add', [
            'class'     => 'comment-add form',
            'message'   => 'Login to comment',
            'loggedIn'  => empty($user) ? null : $user['id'],
            'content'   => $form->getHTML()
        ]);
    }
}
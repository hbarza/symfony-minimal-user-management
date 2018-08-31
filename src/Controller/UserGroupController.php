<?php

namespace App\Controller;

use App\Entity\UserGroup;
use App\Form\UserGroupType;
use App\Repository\UserGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/group")
 */
class UserGroupController extends AbstractController
{
    /**
     * @Route("/", name="user_group_index", methods="GET")
     */
    public function index(UserGroupRepository $userGroupRepository): Response
    {
        return $this->render('user_group/index.html.twig', ['user_groups' => $userGroupRepository->findAll()]);
    }

    /**
     * @Route("/new", name="user_group_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $userGroup = new UserGroup();
        $form = $this->createForm(UserGroupType::class, $userGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userGroup->setCreateAt(new \Datetime);
            $userGroup->setUpdateAt(new \Datetime);
            $em = $this->getDoctrine()->getManager();
            $em->persist($userGroup);
            $em->flush();

            $this->addFlash('success', 'New group was added');
            return $this->redirectToRoute('user_group_index');
        }

        return $this->render('user_group/new.html.twig', [
            'user_group' => $userGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_group_show", methods="GET")
     */
    public function show(UserGroup $userGroup): Response
    {
        return $this->render('user_group/show.html.twig', ['user_group' => $userGroup]);
    }

    /**
     * @Route("/{id}/edit", name="user_group_edit", methods="GET|POST")
     */
    public function edit(Request $request, UserGroup $userGroup): Response
    {
        $form = $this->createForm(UserGroupType::class, $userGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Group was changed');
            return $this->redirectToRoute('user_group_edit', ['id' => $userGroup->getId()]);
        }

        return $this->render('user_group/edit.html.twig', [
            'user_group' => $userGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_group_delete", methods="DELETE")
     */
    public function delete(Request $request, UserGroup $userGroup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userGroup->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            if (!$userGroup->getUser()->count()) {
                $em->remove($userGroup);
                $em->flush();
                $this->addFlash('success', 'Group was removed');
            }
            else {
                $this->addFlash("error", 'You can not remove this group, some users are assigned to it.');
            }
        }

        return $this->redirectToRoute('user_group_index');
    }
}

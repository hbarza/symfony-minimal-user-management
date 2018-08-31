<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Entity\UserGroup;
use App\Form\UserEntityType;
use App\Repository\UserEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/entity")
 */
class UserEntityController extends AbstractController
{
    /**
     * @Route("/", name="user_entity_index", methods="GET")
     */
    public function index(UserEntityRepository $userEntityRepository): Response
    {
        return $this->render('user_entity/index.html.twig', ['user_entities' => $userEntityRepository->findAll()]);
    }

    /**
     * @Route("/new", name="user_entity_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $userEntity = new UserEntity();
        $form = $this->createForm(UserEntityType::class, $userEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userEntity->setCreateAt(new \Datetime);
            $userEntity->setUpdateAt(new \Datetime);
            $em = $this->getDoctrine()->getManager();
            $em->persist($userEntity);
            $userEntity->saveUserGroups(
                $request->request->all()['user_entity']['user_groups'],
                $this->getDoctrine()
            );
            $em->flush();

            $this->addFlash('success', 'New user was added');
            return $this->redirectToRoute('user_entity_index');
        }

        return $this->render('user_entity/new.html.twig', [
            'user_entity' => $userEntity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_entity_show", methods="GET")
     */
    public function show(UserEntity $userEntity): Response
    {
        $userGroups = $userEntity->getUserGroups();
        return $this->render('user_entity/show.html.twig', [
            'user_entity' => $userEntity,
            'user_groups' => $userGroups
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_entity_edit", methods="GET|POST")
     */
    public function edit(Request $request, UserEntity $userEntity): Response
    {
        $form = $this->createForm(UserEntityType::class, $userEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userEntity->saveUserGroups(
                $request->request->all()['user_entity']['user_groups'],
                $this->getDoctrine()
            );
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'User was changed');
            return $this->redirectToRoute('user_entity_edit', ['id' => $userEntity->getId()]);
        }

        return $this->render('user_entity/edit.html.twig', [
            'user_entity' => $userEntity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_entity_delete", methods="DELETE")
     */
    public function delete(Request $request, UserEntity $userEntity): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userEntity->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userEntity);
            $em->flush();
        }

        $this->addFlash('success', 'User was removed');
        return $this->redirectToRoute('user_entity_index');
    }
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * @Route("/api/posts/{id}", name="show_post")
     * @Method({"GET"})
     */
    public function showAction($id)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);

        if (empty($post)) {

            $response = [
                'code' => 1,
                'message' => 'post not found',
                'error' => null,
                'result' => null
            ];

            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $data = $this->get('jms_serializer')->serialize($post, 'json');

        $response = [
            'code' => 0,
            'message' => 'success',
            'errors' => null,
            'result' => json_decode($data)
        ];

        return new JsonResponse($response, 200);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/posts", name="create_post")
     * @Method({"Post"})
     */
    public function createAction(Request $request) {

        $data = $request->getContent();
        $post = $this->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Post', 'json');

        $validator = $this->get('validator');
        $errors = $validator->validate($post);

        if (count($errors) > 0) {

            $str = '';
            foreach ($errors as $k => $v) {
                $str .= $k . '-' . $v . '; ';
            }

            $response = [

                'code' => 1,
                'message' => 'validate error',
                'error' => $str,
                'result' => null

            ];

            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        $response = [

            'code' => 0,
            'message' => 'Post created',
            'error' => null,
            'result' => null

        ];

        return new JsonResponse($response, 200);

    }

    /**
     * @Route("/api/posts", name="list_posts")
     * @Method({"GET"})
     */
    public function listPost() {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();

        if (!count($posts)) {
            $response = [
                'code' => 1,
                'message' => 'No posts found',
                'error' => null,
                'result' => null
            ];

            return new JsonResponse($response,  Response::HTTP_NOT_FOUND);
        }
    }

}

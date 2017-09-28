<?php

class RouteProvider implements \Betalabs\Engine\RouteProvider
{

    /**
     * Declare routes
     *
     * @param \Aura\Router\Map $map
     * @return void
     */
    public function route(\Aura\Router\Map $map)
    {
        $map->get('test', '/bla/test', function() {

            $token = new \Betalabs\Engine\Auth\Token();

            $token->informToken(
                'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjYzMDY5ODdhNzU2ZDc5MjhmMzA2MWIxMzM2Y2QzMWVmYmJhNGE2MjFjNmRmMzM3ZWIyMGUzMmRmMjQxYmY3MTI3ZjI3MDQwYWFiMDYzZWRjIn0.eyJhdWQiOiIxNSIsImp0aSI6IjYzMDY5ODdhNzU2ZDc5MjhmMzA2MWIxMzM2Y2QzMWVmYmJhNGE2MjFjNmRmMzM3ZWIyMGUzMmRmMjQxYmY3MTI3ZjI3MDQwYWFiMDYzZWRjIiwiaWF0IjoxNTA2NDM2Nzk5LCJuYmYiOjE1MDY0MzY3OTksImV4cCI6MjEwNjQzNjczOSwic3ViIjoiMSIsInNjb3BlcyI6WyIqIl19.g0rWYIpVrEVeJQhiIjZLOTdiookIFBYofWp9hRhxA8IMdMvOM5bD0caOlkNZevCsCThB7ZXHGulxLcqZ0qpEE1x8xHQbIDR2peScjgzqOju86MouQDWsrerqXWDDrOqhYifkLsesSOtE6lpBc6pHxJA5pOO9Q4uPSyNkeKROosMF6ca47q2lh-7mRAPgSkjgKOU-KJ43ULrNLJT6YT6KjTjE-NxSx00BZ0mW2SoQIXRo3gYo7ffp2E4MQLMR5Cfa5lXQQXHob8m2vFS6sG-i8_qVc0naBP_YwIJdIAw3ZUb2sZBqGAobhtrjKOL3sli1YZlA1NHo_YUjKF10PmOdZ3oJAFAml65zJzPqPso0mU4UXAGQLOE8P0yR7IYKm0Y8hsr4NtEtmA21UoUjgride84zhTOS7JPgfebEqc6jjFdMyckkJCBrdXVyB-au3I6hP9qjNy6rT2KEckhnEje2qjqj0gz4mrb5OlQEEPbiZfZIgY79JsfNIU7kTF34H-Zdy6XzFZchhxJ4kMCnDy_IRMawQI82HfoCqA19oizQx2GTTsLEwuawSwgyOD5U6LAvEgNhcHIp05jQORoLYwSA3NI1u1RfPI9Qb9qn_V7cRUZvYYZfrQj0wzXEzbNQaw-9FgWE-jlN-nhYRabjKRMDkDloZd0ECWWp7Oar2SdJUXg',
                'def502001a1a16913b2351deb3e713402b96a9f8286c2cb93cfd05065063909338b80cae8bb931288c9ea70abf008a572f1dca5394d0deebe1a11c896d067899d824de87cd8e63298dbf9b199b731939cbdac848133c3a705684ef1c0ca13d1f9f3c92069b556c6b7cafa82b9459e6eed7184899b9571b0f2de03cf3fb2b4800c7277fbefae5b33c25ae432149586048456cfdd2a6feb1bedbde8a115f08a310cc9cd4fa59f9cb8c6b229210de1f415169dde8d3f0f24de0f8fa4571340f313b231c2fa925e01dd5dc5c968f46980c787e694f7248b1282e4ffdf0d65664fbcf820fcfc570a68ca3870159020be88f58e19105e75e5deeaeb7358f7dd1c0960e89f0f44d35cdec332cc50f41b0c0f34f218223c8574ab02206b6ac0dd410202b4ecce86ca9a7e940d6c1361b94e11a285af17006eb85f51b3dcacde45bc0fcff82047631b65cf743adf9b19431bcf4e0f36c98a151ecc3681088a717b68a083dd0f4bf8675',
                \Carbon\Carbon::now()->addHour(12)
            );

            $token->refreshToken();

        });
    }

}
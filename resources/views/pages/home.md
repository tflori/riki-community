# heading 1
## heading 2
### heading 3
#### heading 4
##### heading 5
###### heading 6

**Lists**

- unsorted
- bullet points
- Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis
natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec,
pellentesque eu, pretium quis, sem.
-
- above was empty
    - deeper entries

1. sorted
2. numbered
3. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo,
rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras
dapibus. Vivamus elementum semper nisi.
4.
5. above was empty
    1. first item under fifth element
6. item


Example Code:

```php
<?php

namespace App\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Queue Dispatcher
 *
 * The queue dispatcher is a simple one class middleware dispatcher.
 *
 * A handler in queue can either be a string in form of 'Controller@method', a class name
 * of a middleware or request
 * handler or a callable that may act as request handler or middleware.
 * 
 * @package App\Http
 */
class Dispatcher implements RequestHandlerInterface
{
    /** @var array */
    protected $queue;

    /** @var callable */
    protected $resolver;

    /**
     * Dispatcher constructor.
     *
     * @param array $queue
     * @param callable $resolver
     */
    public function __construct(array $queue, callable $resolver)
    {
        $this->queue = $queue;
        $this->resolver = $resolver;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (empty($this->queue)) {
            throw new \LogicException('Queue is empty');
        }

        $handler = array_shift($this->queue);
        if (!$handler instanceof MiddlewareInterface && 
            !$handler instanceof RequestHandlerInterface
        ) {
            $handler = call_user_func($this->resolver, $handler);
        }

        if ($handler instanceof MiddlewareInterface) {
            return $handler->process($request, $this);
        }

        if ($handler instanceof RequestHandlerInterface) {
            return $handler->handle($request);
        }

        return $handler($request, $this);
    }
}
```

Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem
ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum.
Aenean imperdiet. Etiam ultricies nisi vel augue.

Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus,
sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit
id, lorem. Maecenas nec odio et ante tincidunt tempus.

Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt.
Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales,
augue velit cursus nunc, quis gravida magna mi a libero.

Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Nullam accumsan
lorem in dui. Cras ultricies mi eu turpis hendrerit fringilla. Vestibulum ante ipsum primis in faucibus orci luctus et
ultrices posuere cubilia Curae; In ac dui quis mi consectetuer lacinia.

Nam pretium turpis et arcu. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Sed aliquam
ultrices mauris. Integer ante arcu, accumsan a, consectetuer eget, posuere ut, mauris. Praesent adipiscing. Phasellus
ullamcorper ipsum rutrum nunc. Nunc nonummy metus.

Vestibulum volutpat pretium libero. Cras id dui. Aenean ut eros et nisl sagittis vestibulum. Nullam nulla eros,
ultricies sit amet, nonummy id, imperdiet feugiat, pede. Sed lectus. Donec mollis hendrerit risus. Phasellus nec sem in
justo pellentesque facilisis. Etiam imperdiet imperdiet orci.

Nunc nec neque. Phasellus leo dolor, tempus non, auctor et, hendrerit quis, nisi. Curabitur ligula sapien, tincidunt
non, euismod vitae, posuere imperdiet, leo. Maecenas malesuada. Praesent congue erat at massa. Sed cursus turpis vitae
tortor. Donec posuere vulputate arcu. Phasellus accumsan cursus velit. Vestibulum ante ipsum primis in faucibus orci
luctus et ultrices posuere cubilia Curae; Sed aliquam, nisi quis porttitor congue, elit erat euismod orci, ac

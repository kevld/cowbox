<?php
/**
 * Created by PhpStorm.
 * User: moi
 * Date: 23/07/15
 * Time: 16:23
 */


namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadUserData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setUsername('admin');
        $user->setPlainPassword('admin');
        $user->setEmail('admin@cowbox.org');
        $user->addRole('ROLE_ADMIN');
        $user->setEnabled(1);
        $this->setReference('admin', $user);
        $manager->persist($user);

        $user2 = new User();
        $user2->setUsername('user');
        $user2->setPlainPassword('user');
        $user2->setEmail('user@cowbox.org');
        $user2->addRole('ROLE_USER');
        $user2->setEnabled(1);
        $this->setReference('user', $user2);
        $manager->persist($user2);

        $manager->flush();

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 100; // the order in which fixtures will be loaded
    }


}

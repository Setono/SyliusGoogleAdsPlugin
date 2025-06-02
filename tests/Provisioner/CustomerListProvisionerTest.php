<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Tests\Provisioner;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusGoogleAdsPlugin\Factory\CustomerListFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Setono\SyliusGoogleAdsPlugin\Provisioner\CustomerListProvisioner;
use Setono\SyliusGoogleAdsPlugin\Repository\CustomerListRepositoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @covers \Setono\SyliusGoogleAdsPlugin\Provisioner\CustomerListProvisioner
 */
final class CustomerListProvisionerTest extends TestCase
{
    use ProphecyTrait;

    private CustomerListProvisioner $provisioner;

    /** @var ObjectProphecy<ChannelRepositoryInterface> */
    private ObjectProphecy $channelRepository;

    /** @var ObjectProphecy<CustomerListRepositoryInterface> */
    private ObjectProphecy $customerListRepository;

    /** @var ObjectProphecy<CustomerListFactoryInterface> */
    private ObjectProphecy $customerListFactory;

    protected function setUp(): void
    {
        $this->channelRepository = $this->prophesize(ChannelRepositoryInterface::class);
        $this->customerListRepository = $this->prophesize(CustomerListRepositoryInterface::class);
        $this->customerListFactory = $this->prophesize(CustomerListFactoryInterface::class);

        $this->provisioner = new CustomerListProvisioner(
            $this->channelRepository->reveal(),
            $this->customerListRepository->reveal(),
            $this->customerListFactory->reveal(),
        );
    }

    /**
     * @test
     */
    public function it_provisions_customer_lists_for_enabled_channels_without_existing_lists(): void
    {
        $channel1 = $this->prophesize(ChannelInterface::class);
        $channel1->getName()->willReturn('Web Store');

        $channel2 = $this->prophesize(ChannelInterface::class);
        $channel2->getName()->willReturn('Mobile App');

        $customerList1 = $this->prophesize(CustomerListInterface::class);
        $customerList2 = $this->prophesize(CustomerListInterface::class);

        $this->channelRepository->findBy(['enabled' => true])
            ->willReturn([$channel1->reveal(), $channel2->reveal()]);

        // Both channels don't have existing customer lists
        $this->customerListRepository->hasAtLeastOneForChannel($channel1->reveal())
            ->willReturn(false);
        $this->customerListRepository->hasAtLeastOneForChannel($channel2->reveal())
            ->willReturn(false);

        // Factory should create new customer lists
        $this->customerListFactory->createNew()
            ->willReturn($customerList1->reveal(), $customerList2->reveal());

        // Customer lists should be configured
        $customerList1->setName('Customers on Web Store')->shouldBeCalled();
        $customerList1->setChannel($channel1->reveal())->shouldBeCalled();

        $customerList2->setName('Customers on Mobile App')->shouldBeCalled();
        $customerList2->setChannel($channel2->reveal())->shouldBeCalled();

        // Customer lists should be added to repository
        $this->customerListRepository->add($customerList1->reveal())->shouldBeCalled();
        $this->customerListRepository->add($customerList2->reveal())->shouldBeCalled();

        $this->provisioner->provision();
    }

    /**
     * @test
     */
    public function it_skips_channels_that_already_have_customer_lists(): void
    {
        $channel1 = $this->prophesize(ChannelInterface::class);
        $channel1->getName()->willReturn('Web Store');

        $channel2 = $this->prophesize(ChannelInterface::class);
        $channel2->getName()->willReturn('Mobile App');

        $customerList = $this->prophesize(CustomerListInterface::class);

        $this->channelRepository->findBy(['enabled' => true])
            ->willReturn([$channel1->reveal(), $channel2->reveal()]);

        // Channel 1 already has customer lists, channel 2 doesn't
        $this->customerListRepository->hasAtLeastOneForChannel($channel1->reveal())
            ->willReturn(true);
        $this->customerListRepository->hasAtLeastOneForChannel($channel2->reveal())
            ->willReturn(false);

        // Factory should only create one customer list (for channel 2)
        $this->customerListFactory->createNew()
            ->willReturn($customerList->reveal())
            ->shouldBeCalledTimes(1);

        // Only channel 2's customer list should be configured
        $customerList->setName('Customers on Mobile App')->shouldBeCalled();
        $customerList->setChannel($channel2->reveal())->shouldBeCalled();

        // Only one customer list should be added
        $this->customerListRepository->add($customerList->reveal())->shouldBeCalled();

        $this->provisioner->provision();
    }

    /**
     * @test
     */
    public function it_does_nothing_when_no_enabled_channels_exist(): void
    {
        $this->channelRepository->findBy(['enabled' => true])
            ->willReturn([]);

        // No customer lists should be created
        $this->customerListFactory->createNew()->shouldNotBeCalled();
        $this->customerListRepository->add(Argument::any())->shouldNotBeCalled();

        $this->provisioner->provision();
    }

    /**
     * @test
     */
    public function it_does_nothing_when_all_enabled_channels_have_customer_lists(): void
    {
        $channel1 = $this->prophesize(ChannelInterface::class);
        $channel2 = $this->prophesize(ChannelInterface::class);

        $this->channelRepository->findBy(['enabled' => true])
            ->willReturn([$channel1->reveal(), $channel2->reveal()]);

        // Both channels already have customer lists
        $this->customerListRepository->hasAtLeastOneForChannel($channel1->reveal())
            ->willReturn(true);
        $this->customerListRepository->hasAtLeastOneForChannel($channel2->reveal())
            ->willReturn(true);

        // No customer lists should be created
        $this->customerListFactory->createNew()->shouldNotBeCalled();
        $this->customerListRepository->add(Argument::any())->shouldNotBeCalled();

        $this->provisioner->provision();
    }

    /**
     * @test
     */
    public function it_handles_channel_with_null_name(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getName()->willReturn(null);

        $customerList = $this->prophesize(CustomerListInterface::class);

        $this->channelRepository->findBy(['enabled' => true])
            ->willReturn([$channel->reveal()]);

        $this->customerListRepository->hasAtLeastOneForChannel($channel->reveal())
            ->willReturn(false);

        $this->customerListFactory->createNew()
            ->willReturn($customerList->reveal());

        // Should handle null channel name gracefully
        $customerList->setName('Customers on ')->shouldBeCalled();
        $customerList->setChannel($channel->reveal())->shouldBeCalled();

        $this->customerListRepository->add($customerList->reveal())->shouldBeCalled();

        $this->provisioner->provision();
    }

    /**
     * @test
     */
    public function it_handles_empty_channel_name(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getName()->willReturn('');

        $customerList = $this->prophesize(CustomerListInterface::class);

        $this->channelRepository->findBy(['enabled' => true])
            ->willReturn([$channel->reveal()]);

        $this->customerListRepository->hasAtLeastOneForChannel($channel->reveal())
            ->willReturn(false);

        $this->customerListFactory->createNew()
            ->willReturn($customerList->reveal());

        // Should handle empty channel name gracefully
        $customerList->setName('Customers on ')->shouldBeCalled();
        $customerList->setChannel($channel->reveal())->shouldBeCalled();

        $this->customerListRepository->add($customerList->reveal())->shouldBeCalled();

        $this->provisioner->provision();
    }

    /**
     * @test
     */
    public function it_processes_large_number_of_channels(): void
    {
        $channels = [];
        $customerLists = [];

        // Create 50 channels without existing customer lists
        for ($i = 1; $i <= 50; ++$i) {
            $channel = $this->prophesize(ChannelInterface::class);
            $channel->getName()->willReturn("Channel {$i}");
            $channels[] = $channel->reveal();

            $customerList = $this->prophesize(CustomerListInterface::class);
            $customerLists[] = $customerList->reveal();

            $this->customerListRepository->hasAtLeastOneForChannel($channel->reveal())
                ->willReturn(false);

            $customerList->setName("Customers on Channel {$i}")->shouldBeCalled();
            $customerList->setChannel($channel->reveal())->shouldBeCalled();
            $this->customerListRepository->add($customerList->reveal())->shouldBeCalled();
        }

        $this->channelRepository->findBy(['enabled' => true])
            ->willReturn($channels);

        $this->customerListFactory->createNew()
            ->willReturn(...$customerLists);

        $this->provisioner->provision();
    }
}

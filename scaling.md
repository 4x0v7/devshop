Scaling DevShop
===============

Being based on Aegir, DevShop has two methods for scaling a site's servers:

Web Packs
---------

*Web Packs* use NFS to sync the entire `/var/aegir/projects` folder to a group of servers.

For more information on Web Packs, see the Aegir documentation page at [aegir.readthedocs.org/en/3.x/remote-servers/web-packs/](http://aegir.readthedocs.org/en/3.x/remote-servers/web-packs/). 

**Pros:** 

- Depending on where your mount points are, all codebases for all environments are shared with every server in the group. This makes setup & management simpler.
- No need to create new NFS shares per environment.

**Cons:**

- Extra steps needed to install NFS client & server.
- Introduces a "single point of failure" if the NFS system fails.  
- Storing executable code on NFS has been reported to result in poor performance. (See articles like [NFS performance degredation](http://drupal.stackexchange.com/questions/97705/drupal-files-on-nfs-performance-degredation) and [Drupal on NFS](http://serverfault.com/questions/423981/drupal-on-an-nfs-share-has-terrible-performance)
- Special configuration needed to mitigate performance issues.
- All codebases are shared with every server in the group. This might not be ideal.
- Manual setup of load balancer is needed.

Web Clusters
------------

*Web Clusters* uses Rsync to copy an environment's files to a group of servers.

For more information on Web Clusters, see the documentation page at [aegir.readthedocs.org/en/3.x/remote-servers/web-clusters/](http://aegir.readthedocs.org/en/3.x/remote-servers/web-clusters/).

**Pros:** 
    
- Simpler to use out of the box: no additional packages needed.
- RSync is a simple and reliable of delivering code to the servers.
- Source code is on disk, no performance overhead.
- No single point of failure.

**Cons:**

- Drupal files folder is not synced across servers out of the box. Requires a manual solution.
- Shared files folder needs to be setup for every environment.
- Manual setup of load balancer is needed.
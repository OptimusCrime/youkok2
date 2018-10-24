#!/usr/bin/env python
# -*- coding: utf-8 -*-

'''
This file is used to generate the dependency graph wintin Youkok2 to make it easier for the author to see
what depends on what.

Upload stdout to http://www.webgraphviz.com
'''

import os
import re


FILE_TEMPLATE = """
digraph G {
<CONTENT>
}
"""

DI_FILE = os.path.join(
    os.path.dirname(os.path.abspath(__file__)),
    '..',
    'youkok2',
    'src',
    'Common',
    'Containers',
    'Services.php'
)

CONTAINER_EXP = r"new\W(?P<container>\w*)\((?P<group>[\s\nA-Za-z\$\-\>\'\,\(\)\: ]*)\)\;"
INJECTION_EXP = r"(?:get\(\'?(?P<inject>(?:\w*)|\w*)(?:\:\:class)?\'?\))+"


def get_injections(injections_raw):
    if injections_raw is None or injections_raw == '':
        return []

    injections = re.findall(INJECTION_EXP, injections_raw)

    return [] if injections is None or len(injections) == 0 else injections


def map_container(container):
    return {
        'name': container[0],
        'injections': get_injections(container[1])
    }


def get_containers(content):
    containers_raw = re.findall(CONTAINER_EXP, content)

    return [map_container(container) for container in containers_raw]


def save_graph(containers):
    unrolled_graph = []

    for container in containers:
        if len(container['injections']) == 0:
            unrolled_graph.append('    "{}"'.format(container['name']))

        for injection in container['injections']:
            unrolled_graph.append('    "{}" -> "{}"'.format(container['name'], injection))

        # For grouping the containers together
        unrolled_graph.append('')

    graph = "\n".join(unrolled_graph)

    output = FILE_TEMPLATE.replace('<CONTENT>', graph)

    print(output)


def main():
    with open(DI_FILE) as f:
        content = f.read()

    save_graph(get_containers(content))


if __name__ == '__main__':
    main()
